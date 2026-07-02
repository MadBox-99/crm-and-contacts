<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use Carbon\CarbonInterface;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;
use Throwable;

final class GoogleCalendarService
{
    private ?GoogleCalendar $calendarService = null;

    /**
     * Create a Google Calendar client using service account credentials.
     */
    public function getClient(): GoogleCalendar
    {
        if ($this->calendarService instanceof GoogleCalendar) {
            return $this->calendarService;
        }

        $client = new GoogleClient;
        $client->setApplicationName(config('app.name'));

        $credentialsPath = config('services.google.calendar_credentials');

        if ($credentialsPath && file_exists($credentialsPath)) {
            $client->setAuthConfig($credentialsPath);
        }

        $client->addScope(GoogleCalendar::CALENDAR);

        $this->calendarService = new GoogleCalendar($client);

        return $this->calendarService;
    }

    /**
     * Sync a Task to Google Calendar as an event.
     */
    public function syncTask(Task $task, string $calendarId = 'primary'): ?string
    {
        try {
            $calendar = $this->getClient();

            $event = new GoogleEvent;
            $event->setSummary($task->title);
            $event->setDescription($task->description ?? '');

            $startDateTime = new EventDateTime;
            $endDateTime = new EventDateTime;

            if ($task->due_date) {
                $startDateTime->setDate($task->due_date->format('Y-m-d'));
                $endDateTime->setDate($task->due_date->addDay()->format('Y-m-d'));
            } else {
                $startDateTime->setDate(now()->format('Y-m-d'));
                $endDateTime->setDate(now()->addDay()->format('Y-m-d'));
            }

            $event->setStart($startDateTime);
            $event->setEnd($endDateTime);

            $createdEvent = $calendar->events->insert($calendarId, $event);

            return $createdEvent->getId();
        } catch (Throwable $throwable) {
            Log::error('GoogleCalendar: Failed to sync task', [
                'task_id' => $task->id,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a follow-up event on Google Calendar.
     */
    public function createFollowUp(
        string $title,
        CarbonInterface $date,
        ?string $description = null,
        string $calendarId = 'primary',
    ): ?string {
        try {
            $calendar = $this->getClient();

            $event = new GoogleEvent;
            $event->setSummary($title);
            $event->setDescription($description ?? '');

            $startDateTime = new EventDateTime;
            $startDateTime->setDate($date->format('Y-m-d'));

            $endDateTime = new EventDateTime;
            $endDateTime->setDate($date->addDay()->format('Y-m-d'));

            $event->setStart($startDateTime);
            $event->setEnd($endDateTime);

            $createdEvent = $calendar->events->insert($calendarId, $event);

            return $createdEvent->getId();
        } catch (Throwable $throwable) {
            Log::error('GoogleCalendar: Failed to create follow-up', [
                'title' => $title,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Delete an event from Google Calendar.
     */
    public function deleteEvent(string $eventId, string $calendarId = 'primary'): bool
    {
        try {
            $calendar = $this->getClient();
            $calendar->events->delete($calendarId, $eventId);

            return true;
        } catch (Throwable $throwable) {
            Log::error('GoogleCalendar: Failed to delete event', [
                'event_id' => $eventId,
                'error' => $throwable->getMessage(),
            ]);

            return false;
        }
    }
}
