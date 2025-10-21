<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAttribute;
use App\Models\CustomerContact;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            PermissionSeeder::class,
        ]);

        // Create users
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole(Role::Admin);

        User::factory(5)->create();

        // Create product categories
        $categories = ProductCategory::factory(5)->create();

        // Create products
        Product::factory(20)->create([
            'category_id' => fn () => $categories->random()->id,
        ]);

        // Create customers with related data
        Customer::factory(50)
            ->has(CustomerContact::factory(2), 'contacts')
            ->has(CustomerAddress::factory()->billing()->default(), 'addresses')
            ->has(CustomerAddress::factory()->shipping(), 'addresses')
            ->has(CustomerAttribute::factory(3), 'attributes')
            ->has(Opportunity::factory(2), 'opportunities')
            ->create();
        $this->call([
            OrderSeeder::class,
        ]);
        $this->command->info('Database seeded successfully!');
    }
}
