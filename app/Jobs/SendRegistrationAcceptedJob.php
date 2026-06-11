<?php

namespace App\Jobs;

use App\Jobs\Concerns\AppliesOutgoingEmailDelay;
use App\Enums\EmailLogStatus;
use App\Enums\EmailNotificationType;
use App\Enums\FormAnswerReviewStatus;
use App\Mail\RegistrationAcceptedMail;
use App\Models\EmailLog;
use App\Models\FormAnswer;
use App\Services\Registration\FormAnswerRecipientResolver;
use App\Services\Registration\RegistrationQrPngGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationAcceptedJob implements ShouldQueue
{
    use AppliesOutgoingEmailDelay;
    use Queueable;

    public function __construct(
        public string $formAnswerId,
    ) {
    }

    public function handle(
        RegistrationQrPngGenerator $qrGenerator,
        FormAnswerRecipientResolver $recipientResolver,
    ): void {
        $submission = FormAnswer::query()
            ->with(['form.event', 'user'])
            ->find($this->formAnswerId);

        if ($submission === null) {
            Log::warning('[SendRegistrationAcceptedJob] FormAnswer not found.', [
                'form_answer_id' => $this->formAnswerId,
            ]);

            return;
        }

        if ($submission->review_status !== FormAnswerReviewStatus::Accepted) {
            Log::warning('[SendRegistrationAcceptedJob] Submission is not accepted.', [
                'form_answer_id' => $submission->id,
                'review_status' => $submission->review_status?->value,
            ]);

            return;
        }

        $registrationCode = $submission->registration_code;
        if ($registrationCode === null || $registrationCode === '') {
            Log::warning('[SendRegistrationAcceptedJob] Missing registration_code.', [
                'form_answer_id' => $submission->id,
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
                'notification_type' => EmailNotificationType::RegistrationAccepted,
                'error_message' => 'No recipient email address configured.',
                'sent_at' => null,
            ]);

            Log::warning('[SendRegistrationAcceptedJob] No recipient email.', [
                'form_answer_id' => $submission->id,
            ]);

            return;
        }

        try {
            $png = $qrGenerator->pngForSubmission($submission->id);

            $this->applyOutgoingEmailJitter();

            Mail::to($recipientEmail)->send(
                new RegistrationAcceptedMail($submission, $png, $registrationCode)
            );

            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $recipientResolver->userIdForLog($submission),
                'recipient_email' => $recipientEmail,
                'status' => EmailLogStatus::Sent,
                'notification_type' => EmailNotificationType::RegistrationAccepted,
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
                'notification_type' => EmailNotificationType::RegistrationAccepted,
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            Log::error('[SendRegistrationAcceptedJob] Email send failed.', [
                'notification_type' => EmailNotificationType::RegistrationAccepted->value,
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
