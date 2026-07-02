<?php

declare(strict_types=1);

namespace App\Filament\Resources\Quotes\Schemas;

use App\Enums\QuoteStatus;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

final class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label(__('Customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('opportunity_id')
                    ->label(__('Opportunity'))
                    ->relationship('opportunity', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('quote_number')
                    ->label(__('Quote number'))
                    ->required()
                    ->scopedUnique(ignoreRecord: true),
                DatePicker::make('issue_date')
                    ->label(__('Issue date'))
                    ->required(),
                DatePicker::make('valid_until')
                    ->label(__('Valid until'))
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->required()
                    ->default(QuoteStatus::Draft)
                    ->options(QuoteStatus::class),
                TextInput::make('subtotal')
                    ->label(__('Subtotal'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label(__('Discount amount'))
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->label(__('Tax amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->label(__('Total'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->columnSpanFull(),
            ]);
    }

    public static function configureWizard(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make(__('Quote details'))
                        ->icon(Heroicon::DocumentText)
                        ->columns(2)
                        ->schema([
                            Select::make('customer_id')
                                ->label(__('Customer'))
                                ->relationship('customer', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            Select::make('opportunity_id')
                                ->label(__('Opportunity'))
                                ->relationship('opportunity', 'title')
                                ->searchable()
                                ->preload(),
                            TextInput::make('quote_number')
                                ->label(__('Quote number'))
                                ->required()
                                ->scopedUnique(ignoreRecord: true),
                            DatePicker::make('issue_date')
                                ->label(__('Issue date'))
                                ->required()
                                ->default(now()),
                            DatePicker::make('valid_until')
                                ->label(__('Valid until'))
                                ->required(),
                            Textarea::make('notes')
                                ->label(__('Notes'))
                                ->columnSpanFull(),
                        ]),
                    Step::make(__('Items'))
                        ->icon(Heroicon::ListBullet)
                        ->afterValidation(function (Get $get, Set $set): void {
                            self::updateQuoteTotals($get, $set);
                        })
                        ->schema([
                            Repeater::make('items')
                                ->label(__('Items'))
                                ->addActionLabel(__('New Item'))
                                ->relationship()
                                ->schema([
                                    Select::make('product_id')
                                        ->label(__('Product'))
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(function (?string $state, Get $get, Set $set): void {
                                            if (! $state) {
                                                return;
                                            }

                                            $product = Product::query()->find($state);

                                            if (! $product) {
                                                return;
                                            }

                                            $set('unit_price', $product->unit_price);
                                            $set('tax_rate', $product->tax_rate);
                                            self::calculateItemTotals($get, $set);
                                        }),
                                    TextInput::make('description')
                                        ->label(__('Description')),
                                    TextInput::make('quantity')
                                        ->label(__('Quantity'))
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(0.01)
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemTotals($get, $set)),
                                    TextInput::make('unit_price')
                                        ->label(__('Unit price'))
                                        ->numeric()
                                        ->prefix('HUF')
                                        ->default(0)
                                        ->minValue(0)
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemTotals($get, $set)),
                                    TextInput::make('discount_percent')
                                        ->label(__('Discount percent'))
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live()
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemTotals($get, $set)),
                                    TextInput::make('discount_amount')
                                        ->label(__('Discount amount'))
                                        ->numeric()
                                        ->prefix('HUF')
                                        ->default(0)
                                        ->readOnly(),
                                    TextInput::make('tax_rate')
                                        ->label(__('Tax rate'))
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateItemTotals($get, $set)),
                                    TextInput::make('total')
                                        ->label(__('Total'))
                                        ->numeric()
                                        ->prefix('HUF')
                                        ->default(0)
                                        ->readOnly(),
                                ])
                                ->columns(2)
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                    $data['team_id'] = resolve('current_team')->getKey();

                                    return $data;
                                }),
                        ]),
                    Step::make(__('Summary'))
                        ->icon(Heroicon::Calculator)
                        ->columns(2)
                        ->schema([
                            Select::make('status')
                                ->label(__('Status'))
                                ->required()
                                ->default(QuoteStatus::Draft)
                                ->options(QuoteStatus::class),
                            TextInput::make('subtotal')
                                ->label(__('Subtotal'))
                                ->numeric()
                                ->prefix('HUF')
                                ->default(0)
                                ->readOnly(),
                            TextInput::make('discount_amount')
                                ->label(__('Discount amount'))
                                ->numeric()
                                ->prefix('HUF')
                                ->default(0)
                                ->readOnly(),
                            TextInput::make('tax_amount')
                                ->label(__('Tax amount'))
                                ->numeric()
                                ->prefix('HUF')
                                ->default(0)
                                ->readOnly(),
                            TextInput::make('total')
                                ->label(__('Total'))
                                ->numeric()
                                ->prefix('HUF')
                                ->default(0)
                                ->readOnly(),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <x-filament::button type="submit" size="sm">
                        {{ __('Create Quote') }}
                    </x-filament::button>
                BLADE))),
            ]);
    }

    private static function calculateItemTotals(Get $get, Set $set): void
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);
        $discountPercent = (float) ($get('discount_percent') ?? 0);
        $taxRate = (float) ($get('tax_rate') ?? 0);

        $discountAmount = ($quantity * $unitPrice) * ($discountPercent / 100);
        $total = ($quantity * $unitPrice) - $discountAmount + (($quantity * $unitPrice - $discountAmount) * ($taxRate / 100));

        $set('discount_amount', number_format($discountAmount, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }

    private static function updateQuoteTotals(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];

        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;

        foreach ($items as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $discPct = (float) ($item['discount_percent'] ?? 0);
            $taxRate = (float) ($item['tax_rate'] ?? 0);

            $lineSubtotal = $qty * $price;
            $lineDiscount = $lineSubtotal * ($discPct / 100);
            $lineTax = ($lineSubtotal - $lineDiscount) * ($taxRate / 100);

            $subtotal += $lineSubtotal;
            $totalDiscount += $lineDiscount;
            $totalTax += $lineTax;
        }

        $total = $subtotal - $totalDiscount + $totalTax;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('discount_amount', number_format($totalDiscount, 2, '.', ''));
        $set('tax_amount', number_format($totalTax, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }
}
