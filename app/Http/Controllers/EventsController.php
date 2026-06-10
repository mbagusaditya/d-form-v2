<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Services\Event\EventService;
use App\Services\Event\UserPortalEventResolver;

class EventsController extends Controller
{
    public function index(EventService $eventService)
    {
        $events = Event::query()
            ->where('status', EventStatus::Published)
            ->whereNull('deleted_at')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn (Event $e) => $eventService->eventToInertiaArray($e))
            ->values()
            ->all();

        return inertia('Event', [
            'events' => $events,
        ]);
    }

    public function show(string $segment, EventService $eventService, UserPortalEventResolver $resolver)
    {
        $event = $resolver->resolvePublished($segment);

        $segment = $event->slug !== null && $event->slug !== ''
            ? $event->slug
            : (string) $event->getKey();

        return inertia('EventDetail', [
            'event' => $eventService->eventToInertiaArray($event),
            'memberPortalEventUrl' => route('dashboard.user.events.show', [
                'event_segment' => $segment,
            ], absolute: false),
        ]);
    }
}
