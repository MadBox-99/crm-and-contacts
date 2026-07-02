<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignResponseType;
use App\Enums\CampaignType;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'team_id',
    'name',
    'description',
    'start_date',
    'end_date',
    'status',
    'campaign_type',
    'budget',
    'actual_cost',
    'clicks',
    'impressions',
    'google_ads_campaign_id',
    'target_audience_criteria',
    'created_by',
])]
final class Campaign extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(CampaignResponse::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(CampaignConversion::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function googleAdsReports(): HasMany
    {
        return $this->hasMany(GoogleAdsReport::class);
    }

    public function targetAudience(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)
            ->withPivot(['added_at', 'added_by', 'notes'])
            ->withTimestamps()
            ->using(CampaignCustomer::class);
    }

    /**
     * Calculate Cost Per Conversion (CPC)
     */
    public function getCostPerConversion(): ?float
    {
        $conversionCount = $this->conversions()->count();

        if ($conversionCount === 0) {
            return null;
        }

        return round((float) $this->actual_cost / $conversionCount, 2);
    }

    /**
     * Calculate Conversion Rate (%)
     */
    public function getConversionRate(): ?float
    {
        if ($this->clicks === 0) {
            return null;
        }

        $conversionCount = $this->conversions()->count();

        return round(($conversionCount / $this->clicks) * 100, 2);
    }

    /**
     * Calculate Return on Investment (ROI %)
     */
    public function getROI(): ?float
    {
        if ((float) $this->actual_cost === 0.0) {
            return null;
        }

        $totalRevenue = (float) $this->conversions()->sum('conversion_value');
        $profit = $totalRevenue - (float) $this->actual_cost;

        return round(($profit / (float) $this->actual_cost) * 100, 2);
    }

    /**
     * Calculate Return on Ad Spend (ROAS)
     */
    public function getROAS(): ?float
    {
        if ((float) $this->actual_cost === 0.0) {
            return null;
        }

        $totalRevenue = (float) $this->conversions()->sum('conversion_value');

        return round($totalRevenue / (float) $this->actual_cost, 2);
    }

    /**
     * Calculate Budget Usage Percentage
     */
    public function getBudgetUsagePercentage(): float
    {
        if ($this->budget === null || (float) $this->budget === 0.0) {
            return 0.0;
        }

        return round(((float) $this->actual_cost / (float) $this->budget) * 100, 2);
    }

    /**
     * Check if campaign is within budget
     */
    public function isWithinBudget(): bool
    {
        if ($this->budget === null) {
            return true;
        }

        return (float) $this->actual_cost <= (float) $this->budget;
    }

    /**
     * Get total conversion value
     */
    public function getTotalConversionValue(): float
    {
        return (float) $this->conversions()->sum('conversion_value');
    }

    /**
     * Get conversion count
     */
    public function getConversionCount(): int
    {
        return $this->conversions()->count();
    }

    /**
     * @return array{total: int, by_type: array<string, int>, response_rate: float}
     */
    public function getResponseAnalysis(): array
    {
        $responses = $this->responses()->get();
        $total = $responses->count();
        $audienceCount = $this->targetAudience()->count();

        $byType = [];
        foreach (CampaignResponseType::cases() as $type) {
            $byType[$type->value] = $responses->where('response_type', $type)->count();
        }

        return [
            'total' => $total,
            'by_type' => $byType,
            'response_rate' => $audienceCount > 0 ? round(($total / $audienceCount) * 100, 2) : 0.0,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'description',
                'start_date',
                'end_date',
                'status',
                'campaign_type',
                'budget',
                'actual_cost',
            ]);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'campaign_type' => CampaignType::class,
            'budget' => 'decimal:2',
            'actual_cost' => 'decimal:2',
            'clicks' => 'integer',
            'impressions' => 'integer',
            'target_audience_criteria' => 'array',
        ];
    }
}
