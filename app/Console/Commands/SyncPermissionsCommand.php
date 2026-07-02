<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Signature('permissions:sync')]
#[Description('Sync all permissions and roles from enums to the database')]
final class SyncPermissionsCommand extends Command
{
    public function handle(): int
    {
        $this->components->info('Syncing permissions and roles...');

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionCount = 0;
        foreach (PermissionEnum::cases() as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
            $permissionCount++;
        }

        $this->components->twoColumnDetail('Permissions synced', (string) $permissionCount);

        foreach (RoleEnum::cases() as $roleEnum) {
            $role = Role::query()->firstOrCreate(['name' => $roleEnum]);
            $role->syncPermissions($roleEnum->permissions());
            $this->components->twoColumnDetail(sprintf('Role [%s]', $roleEnum->value), count($roleEnum->permissions()).' permissions');
        }

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->components->info('Done!');

        return self::SUCCESS;
    }
}
