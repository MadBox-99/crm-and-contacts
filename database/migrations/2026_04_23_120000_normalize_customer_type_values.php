<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('customers')->where('type', 'Individual')->update(['type' => 'individual']);
        DB::table('customers')->where('type', 'Company')->update(['type' => 'company']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `customers` MODIFY `type` VARCHAR(255) NOT NULL DEFAULT 'individual'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `customers` MODIFY `type` VARCHAR(255) NOT NULL DEFAULT 'Individual'");
        }

        DB::table('customers')->where('type', 'individual')->update(['type' => 'Individual']);
        DB::table('customers')->where('type', 'company')->update(['type' => 'Company']);
    }
};
