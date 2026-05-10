<?php

namespace App\Jobs;

use App\Jobs\Concerns\AppliesOutgoingEmailDelay;
use App\Enums\EmailLogStatus;
use App\Enums\EmailNotificationType;
use App\Enums\MemberConfirmationStatus;
use App\Enums\RegistrationRole;
use App\Mail\RegistrationConfirmationMail;
use App\Mail\RegistrationRejectedMail;
use App\Models\EmailLog;
use App\Models\FormAnswer;
use App\Services\Registration\RegistrationAnswersSummarizer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTeamInvitationLeaderNoticeJob implements ShouldQueue
{
    use AppliesOutgoingEmailDelay;
    use Queueable;

    public function __construct(
        public string $memberFormAnswerId,
        public string $decision,
        /** Shown only in the leader decline email; not stored on FormAnswer. */
        public ?string $declineReason = null,
    ) {
    }

    public function handle(RegistrationAnswersSummarizer $summarizer): void
    {
        if (! in_array($this->decision, ['accepted', 'rejected'], true)) {
            Log::warning('[SendTeamInvitationLeaderNoticeJob] Invalid decision.', [
                'decision' => $this->decision,
            ]);

            return;
        }

        $memberSubmission = FormAnswer::query()
            ->with(['form.event', 'user'])
            ->find($this->memberFormAnswerId);

        if ($memberSubmission === null) {
            Log::warning('[SendTeamInvitationLeaderNoticeJob] FormAnswer not found.', [
                'form_answer_id' => $this->memberFormAnswerId,
            ]);

            return;
        }

        if ($memberSubmission->registration_role !== RegistrationRole::Member) {
            Log::warning('[SendTeamInvitationLeaderNoticeJob] Expected a member submission.', [
                'form_answer_id' => $memberSubmission->id,
            ]);

            return;
        }

        $expected = $this->decision === 'accepted'
            ? MemberConfirmationStatus::Accepted
            : MemberConfirmationStatus::Rejected;

        if ($memberSubmission->member_confirmation_status !== $expected) {
            Log::warning('[SendTeamInvitationLeaderNoticeJob] Member confirmation status does not match decision.', [
                'form_answer_id' => $memberSubmission->id,
                'decision' => $this->decision,
                'member_confirmation_status' => $memberSubmission->member_confirmation_status?->value,
            ]);

            return;
        }

        $memberSubmission->loadMissing('teamLeaderSubmission.user');
        $leaderSubmission = $memberSubmission->teamLeaderSubmission;
        $leader = $leaderSubmission?->user;

        if ($leader === null || $leader->email === '') {
            if ($leader !== null && $leader->email === '') {
                $event = $memberSubmission->form->event;
                EmailLog::query()->create([
                    'form_answer_id' => $memberSubmission->id,
                    'event_id' => $event->id,
                    'user_id' => $leader->id,
                    'recipient_email' => '',
                    'status' => EmailLogStatus::Failed,
                    'notification_type' => $this->decision === 'accepted'
                        ? EmailNotificationType::TeamMemberInvitationAcceptedLeaderNotice
                        : EmailNotificationType::TeamMemberInvitationDeclinedLeaderNotice,
                    'error_message' => 'Leader has no email address configured.',
                    'sent_at' => null,
                ]);
            }

            Log::warning('[SendTeamInvitationLeaderNoticeJob] No leader or leader email.', [
                'form_answer_id' => $memberSubmission->id,
            ]);

            return;
        }

        $event = $memberSubmission->form->event;
        $notificationType = $this->decision === 'accepted'
            ? EmailNotificationType::TeamMemberInvitationAcceptedLeaderNotice
            : EmailNotificationType::TeamMemberInvitationDeclinedLeaderNotice;

        try {
            $this->applyOutgoingEmailJitter();

            if ($this->decision === 'accepted') {
                $answersSummary = $summarizer->summarize($memberSubmission);
                Mail::to($leader->email)->send(
                    new RegistrationConfirmationMail(
                        $memberSubmission,
                        $answersSummary,
                        null,
                        $leader,
                        true,
                    )
                );
            } else {
                Mail::to($leader->email)->send(
                    new RegistrationRejectedMail(
                        $memberSubmission,
                        forTeammateDeclinedLeaderNotice: true,
                        greetingUser: $leader,
                        declineReason: $this->declineReason !== null && $this->declineReason !== ''
                            ? $this->declineReason
                            : null,
                    )
                );
            }

            EmailLog::query()->create([
                'form_answer_id' => $memberSubmission->id,
                'event_id' => $event->id,
                'user_id' => $leader->id,
                'recipient_email' => $leader->email,
                'status' => EmailLogStatus::Sent,
                'notification_type' => $notificationType,
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            EmailLog::query()->create([
                'form_answer_id' => $memberSubmission->id,
                'event_id' => $event->id,
                'user_id' => $leader->id,
                'recipient_email' => $leader->email,
                'status' => EmailLogStatus::Failed,
                'notification_type' => $notificationType,
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            Log::error('[SendTeamInvitationLeaderNoticeJob] Email send failed.', [
                'notification_type' => $notificationType->value,
                'form_answer_id' => $memberSubmission->id,
                'event_id' => $event->id,
                'recipient_email' => $leader->email,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
