<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintEscalation;
use App\Models\Customer;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class ComplaintService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createComplaint(Customer $customer, array $data): Complaint
    {
        return DB::transaction(function () use ($customer, $data): Complaint {
            $severity = $data['severity'] instanceof ComplaintSeverity
                ? $data['severity']
                : ComplaintSeverity::from($data['severity']);

            return Complaint::query()->create([
                'customer_id' => $customer->id,
                'complaint_number' => Complaint::generateComplaintNumber(),
                'type' => $data['type'] ?? null,
                'subject' => $data['subject'] ?? null,
                'title' => $data['title'] ?? $data['subject'] ?? 'Complaint',
                'description' => $data['description'],
                'severity' => $severity,
                'status' => ComplaintStatus::Open,
                'order_id' => $data['order_id'] ?? null,
                'reported_by' => $data['reported_by'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'reported_at' => now(),
                'sla_deadline_at' => $this->getSlaDeadline($severity),
                'escalation_level' => 0,
            ]);
        });
    }

    public function escalate(Complaint $complaint, User $escalatedTo, User $escalatedBy, string $reason): ComplaintEscalation
    {
        return DB::transaction(function () use ($complaint, $escalatedTo, $escalatedBy, $reason): ComplaintEscalation {
            $escalation = ComplaintEscalation::query()->create([
                'complaint_id' => $complaint->id,
                'escalated_to' => $escalatedTo->id,
                'escalated_by' => $escalatedBy->id,
                'reason' => $reason,
                'escalated_at' => now(),
            ]);

            $complaint->update([
                'assigned_to' => $escalatedTo->id,
                'status' => ComplaintStatus::InProgress,
                'escalation_level' => $complaint->escalation_level + 1,
            ]);

            return $escalation;
        });
    }

    public function resolve(Complaint $complaint, string $resolution): Complaint
    {
        $complaint->update([
            'status' => ComplaintStatus::Resolved,
            'resolution' => $resolution,
            'resolved_at' => now(),
        ]);

        return $complaint->refresh();
    }

    /**
     * @return Collection<int, Complaint>
     */
    public function getOverdueSlaComplaints(): Collection
    {
        return Complaint::query()
            ->whereNotNull('sla_deadline_at')
            ->where('sla_deadline_at', '<', now())
            ->whereNotIn('status', [ComplaintStatus::Resolved, ComplaintStatus::Closed])
            ->get();
    }

    /**
     * @return array<string, int>
     */
    public function getStatisticsByType(): array
    {
        return Complaint::query()
            ->selectRaw('type, count(*) as count')
            ->whereNotNull('type')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    public function getSlaDeadline(ComplaintSeverity $severity): CarbonInterface
    {
        $hours = match ($severity) {
            ComplaintSeverity::Critical => 4,
            ComplaintSeverity::High => 24,
            ComplaintSeverity::Medium => 72,
            ComplaintSeverity::Low => 168,
        };

        return now()->addHours($hours);
    }
}
