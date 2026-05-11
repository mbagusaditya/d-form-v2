<?php

namespace App\Mail;

use App\Models\FormAnswer;
use App\Models\User;
use App\Support\RegistrationPortalLinks;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmationMail extends Mailable
{
    use SerializesModels;

    /**
     * @param  array<string, string>  $answersSummary  Label => display value
     * @param  string|null  $qrPngBinary                   Omit for team/bundle leaders until an admin accepts the submission
     * @param  bool  $isTeammateConfirmedLeaderNotice      Email to the team leader: teammate confirmed (uses same Blade, different intro)
     */
    public function __construct(
        public FormAnswer $submission,
        public array $answersSummary,
        public ?string $qrPngBinary = null,
        public ?User $greetingUser = null,
        public bool $isTeammateConfirmedLeaderNotice = false,
    ) {
        $this->submission->loadMissing(['form.event', 'user']);
    }

    public function envelope(): Envelope
    {
        $event = $this->submission->form->event;

        return new Envelope(
            subject: $this->isTeammateConfirmedLeaderNotice
                ? __('Team registration update: :title', ['title' => $event->title])
                : __('Registration received: :title', ['title' => $event->title]),
        );
    }

    public function content(): Content
    {
        $showAttendanceQr = ! $this->isTeammateConfirmedLeaderNotice
            && $this->qrPngBinary !== null
            && $this->qrPngBinary !== '';

        $greetingUser = $this->greetingUser ?? $this->submission->user;

        return new Content(
            html: 'mail.registration-confirmation',
            text: 'mail.registration-confirmation-text',
            with: [
                'submission' => $this->submission,
                'event' => $this->submission->form->event,
                'form' => $this->submission->form,
                'user' => $greetingUser,
                'answersSummary' => $this->answersSummary,
                'showAttendanceQr' => $showAttendanceQr,
                'registrationDetailsUrl' => RegistrationPortalLinks::registrationDetailsUrl($this->submission->form->event),
                'isTeammateConfirmedLeaderNotice' => $this->isTeammateConfirmedLeaderNotice,
                'teammateUser' => $this->submission->user,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $showAttendanceQr = ! $this->isTeammateConfirmedLeaderNotice
            && $this->qrPngBinary !== null
            && $this->qrPngBinary !== '';

        if (! $showAttendanceQr) {
            return [];
        }

        return [
            Attachment::fromData(fn () => $this->qrPngBinary, 'qr-code.png')
                ->withMime('image/png')
                ->as('qr-code.png'),
        ];
    }
}
