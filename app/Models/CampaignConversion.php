<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CampaignConversionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'team_id',
    'campaign_id',
    'customer_id',
    'opportunity_id',
    'conversion_date',
    'conversion_value',
    'cost_at_conversion',
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_term',
    'utm_content',
    'google_ads_conversion_id',
    'notes',
])]
final class CampaignConversion extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CampaignConversionFactory> */
    use HasFactory;

    use LogsActivity;

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'campaign_id',
                'customer_id',
                'opportunity_id',
                'conversion_date',
                'conversion_value',
                'cost_at_conversion',
            ]);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'conversion_date' => 'datetime',
            'conversion_value' => 'decimal:2',
            'cost_at_conversion' => 'decimal:2',
        ];
    }
}
