<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CustomerContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CustomerContact extends Model
{
    /** @use HasFactory<CustomerContactFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'position',
        'is_primary',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
