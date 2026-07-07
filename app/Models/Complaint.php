<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ComplaintSeverity;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintType;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\ComplaintFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable([
    'team_id',
    'customer_id',
    'order_id',
    'reported_by',
    'assigned_to',
    'complaint_number',
    'type',
    'subject',
    'title',
    'description',
    'severity',
    'status',
    'resolution',
    'reported_at',
    'resolved_at',
    'sla_deadline_at',
    'escalation_level',
])]
final class Complaint extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<ComplaintFactory> */
    use HasFactory;

    public static function generateComplaintNumber(): string
    {
        $year = now()->format('Y');
        $lastComplaint = self::query()->withoutGlobalScopes()
            ->whereYear('created_at', $year)
            ->whereNotNull('complaint_number')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastComplaint
            ? ((int) mb_substr((string) $lastComplaint->complaint_number, -4)) + 1
            : 1;

        return 'CMP-'.$year.'-'.mb_str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(ComplaintEscalation::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'severity' => ComplaintSeverity::class,
            'status' => ComplaintStatus::class,
            'type' => ComplaintType::class,
            'reported_at' => 'datetime',
            'resolved_at' => 'datetime',
            'sla_deadline_at' => 'datetime',
            'escalation_level' => 'integer',
        ];
    }
}
