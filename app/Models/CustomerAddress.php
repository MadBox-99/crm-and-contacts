<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CustomerAddressFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'team_id',
    'customer_id',
    'type',
    'country',
    'postal_code',
    'city',
    'street',
    'building_number',
    'floor',
    'door',
    'is_default',
])]
final class CustomerAddress extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CustomerAddressFactory> */
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }
}
