<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BugReportStatus;
use App\Enums\ComplaintSeverity;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\BugReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'team_id',
    'user_id',
    'title',
    'description',
    'severity',
    'status',
    'source',
    'assigned_to',
    'resolved_at',
])]
final class BugReport extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<BugReportFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'severity' => ComplaintSeverity::class,
            'status' => BugReportStatus::class,
        ];
    }
}
