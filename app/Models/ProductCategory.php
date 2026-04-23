<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Database\Factories\ProductCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Madbox99\FilamentWooCommerce\Concerns\HasWooMapping;

final class ProductCategory extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<ProductCategoryFactory> */
    use HasFactory;

    use HasWooMapping;

    protected $fillable = [
        'team_id',
        'name',
        'parent_id',
        'description',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
