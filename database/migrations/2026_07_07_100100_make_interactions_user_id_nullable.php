<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interactions', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('interactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('interactions', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Form-submission interactions have no CRM user; they must be removed
        // before the NOT NULL constraint can be restored.
        DB::table('interactions')->whereNull('user_id')->delete();

        Schema::table('interactions', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('interactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('interactions', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
