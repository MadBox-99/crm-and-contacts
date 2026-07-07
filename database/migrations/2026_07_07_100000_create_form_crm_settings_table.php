<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_crm_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_form_id')
                ->unique()
                ->constrained('registration_forms')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->json('field_map')->nullable();
            $table->boolean('create_opportunity')->default(true);
            $table->string('opportunity_stage')->nullable()->default('lead');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->boolean('enable_scoring')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_crm_settings');
    }
};
