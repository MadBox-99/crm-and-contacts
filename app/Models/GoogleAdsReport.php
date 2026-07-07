<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\GoogleAdsReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

#[Fillable([
    'team_id',
    'campaign_id',
    'report_date',
    'metadata',
    'keyword_data',
    'historical_metrics',
    'bulk_results',
    'statistics',
    'raw_data',
])]
final class GoogleAdsReport extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<GoogleAdsReportFactory> */
    use HasFactory;

    use SoftDeletes;

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'report_date' => 'immutable_date',
            'metadata' => 'array',
            'keyword_data' => 'array',
            'historical_metrics' => 'array',
            'bulk_results' => 'array',
            'statistics' => 'array',
            'raw_data' => 'array',
        ];
    }
}
