<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignResponseType;
use Database\Factories\CampaignResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CampaignResponse extends Model
{
    /** @use HasFactory<CampaignResponseFactory> */
    use HasFactory;

    protected $fillable = [
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

    protected function casts(): array
    {
        return [
            'response_type' => CampaignResponseType::class,
            'responded_at' => 'datetime',
        ];
    }
}
