<?php

namespace App\Http\Controllers\Dashboard\Events\Forms;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FormField;
use App\Models\Form;
use App\Models\FormAnswer;
use App\Support\FormFieldTypeMapping;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormSubmissionsController extends Controller
{
    /**
     * List all submissions for an event form (admin-only view).
     *
     * Authorises via the existing EventPolicy `view` ability so only users
     * with the `events.view` permission can access this endpoint.
     */
    public function __invoke(Request $request, Event $event, Form $form): Response
    {
        $this->authorize('view', $event);

        abort_unless($form->event_id === $event->id, 404);

        $submissions = FormAnswer::query()
            ->with(['user:id,name,email', 'reviewer:id,name,email'])
            ->where('form_id', $form->id)
            ->whereListedForOrganizerParticipantRoster()
            ->orderByDesc('created_at')
            ->paginate(25)
            ->through(fn (FormAnswer $answer) => [
                'id'           => $answer->id,
                'user'         => $answer->user
                    ? [
                        'id'    => $answer->user->id,
                        'name'  => $answer->user->name,
                        'email' => $answer->user->email,
                    ]
                    : null,
                'answers'      => $answer->answers,
                'submitted_at' => $answer->created_at->toISOString(),
                'review_status' => $answer->review_status?->value,
                'reviewed_at' => $answer->reviewed_at?->toIso8601String(),
                'reviewed_by' => $answer->reviewed_by,
                'registration_role' => $answer->registration_role?->value,
                'member_confirmation_status' => $answer->member_confirmation_status?->value,
                'group_token' => $answer->group_token,
                'reviewer' => $answer->reviewer
                    ? [
                        'id' => $answer->reviewer->id,
                        'name' => $answer->reviewer->name,
                        'email' => $answer->reviewer->email,
                    ]
                    : null,
            ]);

        return Inertia::render('Dashboard/Events/Forms/Submissions', [
            'event'       => [
                'id'    => $event->id,
                'title' => $event->title,
            ],
            'form'        => [
                'id'    => $form->id,
                'title' => $form->title,
            ],
            'fields'      => $form->formFields()
                ->orderBy('order')
                ->get()
                ->map(fn (FormField $f) => FormFieldTypeMapping::fieldToInertia($f))
                ->values()
                ->all(),
            'submissions' => $submissions,
        ]);
    }
}
