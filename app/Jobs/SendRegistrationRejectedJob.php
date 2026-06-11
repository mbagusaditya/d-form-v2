<?php

namespace App\Jobs;

use App\Jobs\Concerns\AppliesOutgoingEmailDelay;
use App\Enums\EmailLogStatus;
use App\Enums\EmailNotificationType;
use App\Enums\FormAnswerReviewStatus;
use App\Mail\RegistrationRejectedMail;
use App\Models\EmailLog;
use App\Models\FormAnswer;
use App\Services\Registration\FormAnswerRecipientResolver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationRejectedJob implements ShouldQueue
{
    use AppliesOutgoingEmailDelay;
    use Queueable;

    public function __construct(
        public string $formAnswerId,
    ) {
    }

    public function handle(FormAnswerRecipientResolver $recipientResolver): void
    {
        $submission = FormAnswer::query()
            ->with(['form.event', 'user'])
            ->find($this->formAnswerId);

        if ($submission === null) {
            Log::warning('[SendRegistrationRejectedJob] FormAnswer not found.', [
                'form_answer_id' => $this->formAnswerId,
            ]);

            return;
        }

        if ($submission->review_status !== FormAnswerReviewStatus::Rejected) {
            Log::warning('[SendRegistrationRejectedJob] Submission is not rejected.', [
                'form_answer_id' => $submission->id,
                'review_status' => $submission->review_status?->value,
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
                'notification_type' => EmailNotificationType::RegistrationRejected,
                'error_message' => 'No recipient email address configured.',
                'sent_at' => null,
            ]);

            Log::warning('[SendRegistrationRejectedJob] No recipient email.', [
                'form_answer_id' => $submission->id,
            ]);

            return;
        }

        try {
            $this->applyOutgoingEmailJitter();

            Mail::to($recipientEmail)->send(new RegistrationRejectedMail($submission));

            EmailLog::query()->create([
                'form_answer_id' => $submission->id,
                'event_id' => $event->id,
                'user_id' => $recipientResolver->userIdForLog($submission),
                'recipient_email' => $recipientEmail,
                'status' => EmailLogStatus::Sent,
                'notification_type' => EmailNotificationType::RegistrationRejected,
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
                'notification_type' => EmailNotificationType::RegistrationRejected,
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            Log::error('[SendRegistrationRejectedJob] Email send failed.', [
                'notification_type' => EmailNotificationType::RegistrationRejected->value,
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
