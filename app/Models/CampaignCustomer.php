<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Override;

#[Fillable(['campaign_id', 'customer_id', 'added_at', 'added_by', 'notes'])]
#[Table(name: 'campaign_customer')]
final class CampaignCustomer extends Pivot
{
    use HasFactory;
    use HasFactory;

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'added_at' => 'datetime',
        ];
    }
}
