<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\ComplaintEscalationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'team_id',
    'complaint_id',
    'escalated_to',
    'escalated_by',
    'reason',
    'escalated_at',
])]
final class ComplaintEscalation extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<ComplaintEscalationFactory> */
    use HasFactory;

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function escalatedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function escalatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'escalated_at' => 'datetime',
        ];
    }
}
