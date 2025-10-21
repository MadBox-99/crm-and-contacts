<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\RelationManagers\AddressesRelationManager;
use App\Filament\Resources\Customers\RelationManagers\AttributesRelationManager;
use App\Filament\Resources\Customers\RelationManagers\CommunicationsRelationManager;
use App\Filament\Resources\Customers\RelationManagers\ComplaintsRelationManager;
use App\Filament\Resources\Customers\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\Customers\RelationManagers\InteractionsRelationManager;
use App\Filament\Resources\Customers\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\Customers\RelationManagers\OpportunitiesRelationManager;
use App\Filament\Resources\Customers\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\Customers\RelationManagers\QuotesRelationManager;
use App\Filament\Resources\Customers\RelationManagers\TasksRelationManager;
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Customers\Tables\CustomersTable;
use App\Models\Customer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

final class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Customers;

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
            AddressesRelationManager::class,
            AttributesRelationManager::class,
            OpportunitiesRelationManager::class,
            QuotesRelationManager::class,
            OrdersRelationManager::class,
            InvoicesRelationManager::class,
            InteractionsRelationManager::class,
            TasksRelationManager::class,
            ComplaintsRelationManager::class,
            CommunicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
