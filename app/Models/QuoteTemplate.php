<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\QuoteTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable([
    'team_id',
    'name',
    'body',
    'is_default',
    'is_active',
    'created_by',
])]
final class QuoteTemplate extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<QuoteTemplateFactory> */
    use HasFactory;

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quoteVersions(): HasMany
    {
        return $this->hasMany(QuoteVersion::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
