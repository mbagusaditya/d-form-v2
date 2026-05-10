<?php

namespace App\Jobs;

use App\Jobs\Concerns\AppliesOutgoingEmailDelay;
use App\Enums\EmailLogStatus;
use App\Enums\EmailNotificationType;
use App\Enums\MemberConfirmationStatus;
use App\Enums\RegistrationRole;
use App\Mail\RegistrationRejectedMail;
use App\Models\EmailLog;
use App\Models\FormAnswer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInviteeInvitationDeclinedNoticeJob implements ShouldQueue
{
    use AppliesOutgoingEmailDelay;
    use Queueable;

    public function __construct(
        public string $formAnswerId,
    ) {
    }

    public function handle(): void
    {
        $submission = FormAnswer::query()
            ->with(['form.event', 'user'])
            ->find($this->formAnswerId);

        if ($submission === null) {
            Log::warning('[SendInviteeInvitationDeclinedNoticeJob] FormAnswer not found.', [
                'form_answer_id' => $this->formAnswerId,
            ]);

            return;
        }

        if ($submission->registration_role !== RegistrationRole::Member
            || $submission->member_confirmation_status !== MemberConfirmationStatus::Rejected) {
            Log::warning('[SendInviteeInvitationDeclinedNoticeJob] Expected a member submission declined by invitee; skipping.', [
                'form_answer_id' => $submission->id,
                'registration_role' => $submission->registration_role?->value,
                'member_confirmation_status' => $submission->member_confirmation_status?->value,
            ]);

            return;
        }

        $user = $submission->user;
        if ($user === null) {
            Log::warning('[SendInviteeInvitationDeclinedNoticeJob] Submission has no user.', [
                'form_answer_id' => $submission->id,
            ]);

            return;
        }

        $event = $submission->form->event;

        if ($user->email === '') {
            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $user->id,
                'recipient_email' => '',
                'status' => EmailLogStatus::Failed,
                'notification_type' => EmailNotificationType::InvitationDeclinedByInvitee,
                'error_message' => 'User has no email address configured.',
                'sent_at' => null,
            ]);

            Log::warning('[SendInviteeInvitationDeclinedNoticeJob] No recipient email.', [
                'form_answer_id' => $submission->id,
            ]);

            return;
        }

        try {
            $this->applyOutgoingEmailJitter();

            Mail::to($user->email)->send(
                new RegistrationRejectedMail(
                    $submission,
                    forInvitationSelfDeclined: true,
                )
            );

            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'status' => EmailLogStatus::Sent,
                'notification_type' => EmailNotificationType::InvitationDeclinedByInvitee,
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'status' => EmailLogStatus::Failed,
                'notification_type' => EmailNotificationType::InvitationDeclinedByInvitee,
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            Log::error('[SendInviteeInvitationDeclinedNoticeJob] Email send failed.', [
                'notification_type' => EmailNotificationType::InvitationDeclinedByInvitee->value,
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'recipient_email' => $user->email,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
