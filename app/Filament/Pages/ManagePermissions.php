<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Override;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use UnitEnum;

final class ManagePermissions extends Page
{
    /** @var array<string, array{permissions: array<string>, total: int}> */
    public array $roles = [];

    public int $totalPermissions = 0;

    protected string $view = 'filament.pages.manage-permissions';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Permissions');
    }

    #[Override]
    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole(RoleEnum::Admin) ?? false;
    }

    #[Override]
    public function getTitle(): string
    {
        return __('Manage Permissions');
    }

    public function mount(): void
    {
        $this->loadRoles();
    }

    public function syncPermissions(): void
    {
        App::make(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionEnum::cases() as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        foreach (RoleEnum::cases() as $roleEnum) {
            $role = Role::query()->firstOrCreate(['name' => $roleEnum]);
            $role->syncPermissions($roleEnum->permissions());
        }

        App::make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->loadRoles();

        Notification::make()
            ->success()
            ->title(__('Permissions Synced'))
            ->body(__('All permissions and roles have been synced successfully.'))
            ->send();
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label(__('Sync Permissions'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('Sync Permissions'))
                ->modalDescription(__('This will sync all permissions and roles from the code to the database. Existing assignments will be preserved.'))
                ->action(fn () => $this->syncPermissions()),
        ];
    }

    private function loadRoles(): void
    {
        $this->totalPermissions = Permission::query()->count();

        $this->roles = Role::with('permissions')->get()->map(fn (Role $role): array => [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->sort()->values()->all(),
            'total' => $role->permissions->count(),
        ])->keyBy('name')->all();
    }
}
