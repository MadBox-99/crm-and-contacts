<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\LeadScoreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'team_id',
    'customer_id',
    'score',
    'interaction_score',
    'recency_score',
    'opportunity_score',
    'engagement_score',
    'assigned_to',
    'assigned_at',
    'last_calculated_at',
])]
final class LeadScore extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<LeadScoreFactory> */
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'interaction_score' => 'integer',
            'recency_score' => 'integer',
            'opportunity_score' => 'integer',
            'engagement_score' => 'integer',
            'assigned_at' => 'datetime',
            'last_calculated_at' => 'datetime',
        ];
    }
}
