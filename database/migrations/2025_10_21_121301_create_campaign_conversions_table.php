<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Opportunity;
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
        Schema::create('campaign_conversions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Campaign::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Customer::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Opportunity::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamp('conversion_date');
            $table->decimal('conversion_value', 12, 2)->default(0);
            $table->decimal('cost_at_conversion', 12, 2)->default(0);
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('google_ads_conversion_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_conversions');
    }
};
