<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BugReportStatus;
use App\Enums\ComplaintSeverity;
use Database\Factories\BugReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BugReport extends Model
{
    /** @use HasFactory<BugReportFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'severity',
        'status',
        'source',
        'assigned_to',
        'resolved_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'severity' => ComplaintSeverity::class,
            'status' => BugReportStatus::class,
        ];
    }
}
