<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Enums\EventStatus;
use App\Enums\FormAnswerReviewStatus;
use App\Enums\MemberConfirmationStatus;
use App\Enums\RegistrationRole;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FormAnswer;
use App\Services\Event\EventService;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class MemberDashboardController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {
    }

    public function __invoke(Request $request): InertiaResponse
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $formAnswers = FormAnswer::query()
            ->where('user_id', $userId)
            ->excludeTerminatedInvitationMembers()
            ->with(['form.event', 'user:id,name,email,avatar'])
            ->get();

        $eventIds = $formAnswers->pluck('form.event_id')->unique()->filter();

        $events = Event::query()
            ->whereIn('id', $eventIds)
            ->where('status', EventStatus::Published)
            ->get()
            ->keyBy('id');

        $eventsJoined = $events->count();

        $upcomingEvents = $events->filter(fn (Event $e) => $e->start_date > $today);
        $upcomingEventsCount = $upcomingEvents->count();

        $pendingRegistrationsCount = $formAnswers->filter(function ($answer) {
            return $answer->review_status === FormAnswerReviewStatus::Pending
                || $answer->member_confirmation_status === MemberConfirmationStatus::Pending;
        })->count();

        $acceptedRegistrationsCount = $formAnswers->filter(function ($answer) {
            return $answer->review_status === FormAnswerReviewStatus::Accepted;
        })->count();

        $upcomingEventsArray = $upcomingEvents
            ->sortBy('start_date')
            ->take(5)
            ->map(fn (Event $e) => $this->eventService->eventToInertiaArray($e))
            ->values()
            ->all();

        $pendingInvitations = [];
        $pendingInviteAnswers = $formAnswers->filter(function ($answer) {
            return $answer->registration_role === RegistrationRole::Member
                && $answer->member_confirmation_status === MemberConfirmationStatus::Pending
                && ! empty($answer->invitation_token);
        });

        foreach ($pendingInviteAnswers as $answer) {
            if ($answer->form && $answer->form->event && isset($events[$answer->form->event_id])) {
                $event = $events[$answer->form->event_id];
                $pendingInvitations[] = [
                    'event' => $this->eventService->eventToInertiaArray($event),
                    'invitationUrl' => route('dashboard.user.team-invitations.show', ['token' => $answer->invitation_token], false),
                ];
            }
        }

        $calendarEvents = $events->map(function (Event $event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'category' => $event->category,
                'href' => "/events/joined/events/{$event->slug}",
            ];
        })->values()->all();

        return inertia('Dashboard/User/Index', [
            'stats' => [
                'eventsJoined' => $eventsJoined,
                'upcomingEvents' => $upcomingEventsCount,
                'pendingRegistrations' => $pendingRegistrationsCount,
                'acceptedRegistrations' => $acceptedRegistrationsCount,
            ],
            'upcomingEvents' => $upcomingEventsArray,
            'pendingInvitations' => $pendingInvitations,
            'calendarEvents' => $calendarEvents,
        ]);
    }
}
