<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Enums\QuoteStatus;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Interaction;
use App\Models\Quote;
use App\Models\Task;
use App\Models\User;
use App\Notifications\ComplaintSlaBreachNotification;
use App\Notifications\LeadInactiveNotification;
use App\Notifications\QuoteExpiringNotification;
use App\Notifications\TaskDeadlineNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final class NotificationWorkflowService
{
    /**
     * Run all workflow checks. Intended for scheduled execution.
     *
     * @return array{quotes: int, tasks: int, leads: int, complaints: int}
     */
    public function runAll(): array
    {
        return [
            'quotes' => $this->checkExpiringQuotes(),
            'tasks' => $this->checkApproachingDeadlines(),
            'leads' => $this->checkInactiveLeads(),
            'complaints' => $this->checkSlaBreaches(),
        ];
    }

    /**
     * Check for quotes expiring within 3 days and notify the team.
     */
    public function checkExpiringQuotes(int $daysThreshold = 3): int
    {
        $expiringQuotes = Quote::query()
            ->with('customer')
            ->whereIn('status', [QuoteStatus::Draft, QuoteStatus::Sent, QuoteStatus::Viewed])
            ->whereNotNull('valid_until')
            ->whereBetween('valid_until', [Date::now(), Date::now()->addDays($daysThreshold)])
            ->get();

        $notified = 0;

        foreach ($expiringQuotes as $quote) {
            $usersToNotify = $this->getTeamUsers($quote->team_id);

            foreach ($usersToNotify as $user) {
                $user->notify(new QuoteExpiringNotification($quote));
            }

            $notified++;
        }

        return $notified;
    }

    /**
     * Check for tasks with approaching deadlines (within 2 days).
     */
    public function checkApproachingDeadlines(int $daysThreshold = 2): int
    {
        $approachingTasks = Task::query()
            ->whereNotNull('due_date')
            ->whereNull('completed_at')
            ->whereBetween('due_date', [Date::now(), Date::now()->addDays($daysThreshold)])
            ->get();

        $notified = 0;

        foreach ($approachingTasks as $task) {
            $assignee = $task->assignedUser;
            if ($assignee) {
                $assignee->notify(new TaskDeadlineNotification($task));
                $notified++;
            }
        }

        return $notified;
    }

    /**
     * Check for leads inactive for 7+ days (no interactions).
     */
    public function checkInactiveLeads(int $daysThreshold = 7): int
    {
        $cutoffDate = Date::now()->subDays($daysThreshold);

        $inactiveCustomers = Customer::query()
            ->where('is_active', true)
            ->whereDoesntHave('interactions', function ($query) use ($cutoffDate): void {
                $query->where('interaction_date', '>=', $cutoffDate);
            })
            ->whereHas('opportunities', function ($query): void {
                $query->whereIn('stage', ['lead', 'qualified']);
            })
            ->get();

        $notified = 0;

        foreach ($inactiveCustomers as $customer) {
            $lastInteraction = Interaction::query()
                ->where('customer_id', $customer->id)
                ->latest('interaction_date')
                ->first();

            $inactiveDays = $lastInteraction
                ? (int) $lastInteraction->interaction_date->diffInDays(Date::now())
                : $daysThreshold;

            $usersToNotify = $this->getTeamUsers($customer->team_id);

            foreach ($usersToNotify as $user) {
                $user->notify(new LeadInactiveNotification($customer, $inactiveDays));
            }

            $notified++;
        }

        return $notified;
    }

    /**
     * Check for complaints that have breached their SLA.
     */
    public function checkSlaBreaches(): int
    {
        $overdueComplaints = Complaint::query()
            ->with('customer')
            ->whereNotNull('sla_deadline_at')
            ->where('sla_deadline_at', '<', Date::now())
            ->whereNotIn('status', [ComplaintStatus::Resolved, ComplaintStatus::Closed])
            ->get();

        $notified = 0;

        foreach ($overdueComplaints as $complaint) {
            $assignee = $complaint->assignedUser;
            $usersToNotify = $assignee
                ? collect([$assignee])
                : $this->getTeamUsers($complaint->team_id);

            foreach ($usersToNotify as $user) {
                $user->notify(new ComplaintSlaBreachNotification($complaint));
            }

            $notified++;
        }

        return $notified;
    }

    /**
     * Get all users for a given team.
     *
     * @return Collection<int, User>
     */
    private function getTeamUsers(int $teamId): Collection
    {
        return User::query()
            ->whereHas('teams', fn ($q) => $q->where('teams.id', $teamId))
            ->get();
    }
}
