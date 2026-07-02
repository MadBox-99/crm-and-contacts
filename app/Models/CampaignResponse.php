<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignResponseType;
use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CampaignResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

final class CampaignResponse extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CampaignResponseFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'campaign_id',
        'customer_id',
        'response_type',
        'notes',
        'responded_at',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'response_type' => CampaignResponseType::class,
            'responded_at' => 'datetime',
        ];
    }
}
