<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'name'),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
