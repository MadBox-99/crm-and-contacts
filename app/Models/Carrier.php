<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\CarrierFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

#[Fillable([
    'team_id',
    'name',
    'is_active',
])]
final class Carrier extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CarrierFactory> */
    use HasFactory;

    #[Override]
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
