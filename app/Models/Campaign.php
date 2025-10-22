<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignType;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
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
    ];

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
