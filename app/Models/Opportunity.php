<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OpportunityStage;
use App\Models\Concerns\BelongsToTeam;
use App\Observers\OpportunityObserver;
use Database\Factories\OpportunityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(OpportunityObserver::class)]
#[Fillable([
    'team_id',
    'customer_id',
    'campaign_id',
    'title',
    'description',
    'value',
    'probability',
    'stage',
    'expected_close_date',
    'assigned_to',
])]
final class Opportunity extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<OpportunityFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'value', 'probability', 'stage', 'expected_close_date', 'assigned_to', 'campaign_id']);

    }

    #[Override]
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'probability' => 'integer',
            'expected_close_date' => 'date',
            'stage' => OpportunityStage::class,
        ];
    }
}
