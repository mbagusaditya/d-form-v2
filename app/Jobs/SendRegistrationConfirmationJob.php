<?php

namespace App\Jobs;

use App\Jobs\Concerns\AppliesOutgoingEmailDelay;
use App\Enums\EmailLogStatus;
use App\Enums\EmailNotificationType;
use App\Enums\FormAnswerReviewStatus;
use App\Enums\RegistrationRole;
use App\Mail\RegistrationConfirmationMail;
use App\Models\EmailLog;
use App\Models\FormAnswer;
use App\Services\Registration\FormAnswerRecipientResolver;
use App\Services\Registration\RegistrationAnswersSummarizer;
use App\Services\Registration\RegistrationQrPngGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationConfirmationJob implements ShouldQueue
{
    use AppliesOutgoingEmailDelay;
    use Queueable;

    public function __construct(
        public string $formAnswerId,
    ) {
    }

    public function handle(
        RegistrationAnswersSummarizer $summarizer,
        RegistrationQrPngGenerator $qrGenerator,
        FormAnswerRecipientResolver $recipientResolver,
    ): void {
        $submission = FormAnswer::query()
            ->with(['form.event', 'user'])
            ->find($this->formAnswerId);

        if ($submission === null) {
            Log::warning('[SendRegistrationConfirmationJob] FormAnswer not found.', [
                'form_answer_id' => $this->formAnswerId,
            ]);

            return;
        }

        $recipientEmail = $recipientResolver->email($submission);
        $event = $submission->form->event;

        if ($recipientEmail === null || $recipientEmail === '') {
            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $recipientResolver->userIdForLog($submission),
                'recipient_email' => '',
                'status' => EmailLogStatus::Failed,
                'notification_type' => EmailNotificationType::RegistrationSubmitted,
                'error_message' => 'No recipient email address configured.',
                'sent_at' => null,
            ]);

            Log::warning('[SendRegistrationConfirmationJob] No recipient email.', [
                'form_answer_id' => $submission->id,
            ]);

            return;
        }

        $answersSummary = $summarizer->summarize($submission);

        $isTeamOrBundleLeader = $submission->registration_role === RegistrationRole::Leader;
        $isAccepted = $submission->review_status === FormAnswerReviewStatus::Accepted;

        // Only generate QR code for accepted submissions (except team/bundle leaders)
        $qrPng = ($isTeamOrBundleLeader || ! $isAccepted)
            ? null
            : $qrGenerator->pngForSubmission($submission->id);

        try {
            $this->applyOutgoingEmailJitter();

            Mail::to($recipientEmail)->send(
                new RegistrationConfirmationMail($submission, $answersSummary, $qrPng)
            );

            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $recipientResolver->userIdForLog($submission),
                'recipient_email' => $recipientEmail,
                'status' => EmailLogStatus::Sent,
                'notification_type' => EmailNotificationType::RegistrationSubmitted,
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $recipientResolver->userIdForLog($submission),
                'recipient_email' => $recipientEmail,
                'status' => EmailLogStatus::Failed,
                'notification_type' => EmailNotificationType::RegistrationSubmitted,
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            Log::error('[SendRegistrationConfirmationJob] Email send failed.', [
                'notification_type' => EmailNotificationType::RegistrationSubmitted->value,
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'recipient_email' => $recipientEmail,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
