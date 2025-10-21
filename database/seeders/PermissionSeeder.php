<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create all permissions from enum
        foreach (PermissionEnum::cases() as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions using enum
        foreach (RoleEnum::cases() as $roleEnum) {
            $role = Role::query()->firstOrCreate(['name' => $roleEnum]);

            $role->syncPermissions($roleEnum->permissions());
        }
    }
}
