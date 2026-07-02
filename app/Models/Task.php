<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use App\Observers\TaskNotificationObserver;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[ObservedBy(TaskNotificationObserver::class)]
final class Task extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'customer_id',
        'assigned_to',
        'assigned_by',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'completed_at',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }
}
