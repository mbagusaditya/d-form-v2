<?php

namespace App\Mail;

use App\Models\FormAnswer;
use App\Models\User;
use App\Support\RegistrationPortalLinks;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationRejectedMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public FormAnswer $submission,
        public bool $forInvitationSelfDeclined = false,
        public bool $forTeammateDeclinedLeaderNotice = false,
        public ?User $greetingUser = null,
        /** Optional note from invitee (email only; never persisted on the submission). */
        public ?string $declineReason = null,
    ) {
        $this->submission->loadMissing(['form.event', 'user']);
    }

    public function envelope(): Envelope
    {
        $event = $this->submission->form->event;

        if ($this->forInvitationSelfDeclined) {
            return new Envelope(
                subject: __('Invitation update: :title', ['title' => $event->title]),
            );
        }

        return new Envelope(
            subject: __('Registration update: :title', ['title' => $event->title]),
        );
    }

    public function content(): Content
    {
        $greetingUser = $this->greetingUser ?? $this->submission->user;

        return new Content(
            html: 'mail.registration-rejected',
            text: 'mail.registration-rejected-text',
            with: [
                'submission' => $this->submission,
                'event' => $this->submission->form->event,
                'form' => $this->submission->form,
                'user' => $greetingUser,
                'teammateUser' => $this->submission->user,
                'forInvitationSelfDeclined' => $this->forInvitationSelfDeclined,
                'forTeammateDeclinedLeaderNotice' => $this->forTeammateDeclinedLeaderNotice,
                'declineReason' => $this->declineReason,
                'registrationDetailsUrl' => RegistrationPortalLinks::registrationDetailsUrl($this->submission->form->event),
            ],
        );
    }
}
