<?php

declare(strict_types=1);

use App\Services\CustomerDeduplicator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Merge pre-existing duplicate (team_id, email) customers so the
        // unique index below can be applied without a constraint violation.
        app(CustomerDeduplicator::class)->deduplicate();

        Schema::table('customers', function (Blueprint $table): void {
            $table->unique(['team_id', 'email'], 'customers_team_id_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropUnique('customers_team_id_email_unique');
        });
    }
};
