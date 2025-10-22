<?php

declare(strict_types=1);

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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            // Internal references (NULLABLE - might not exist in CRM yet)
            $table->foreignIdFor(App\Models\Customer::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(App\Models\Order::class)->nullable()->constrained()->nullOnDelete();

            // External system references (from external warehouse/logistics system)
            $table->string('external_customer_id')->nullable()->index();
            $table->string('external_order_id')->nullable()->index();

            // Shipment details
            $table->string('shipment_number')->unique();
            $table->string('carrier')->nullable(); // GLS, DPD, FoxPost, etc.
            $table->string('tracking_number')->nullable()->unique();
            $table->string('status')->default('pending'); // enum in model

            // Address snapshot (JSON)
            $table->json('shipping_address')->nullable();

            // Timestamps from external system
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Additional info
            $table->text('notes')->nullable();
            $table->json('documents')->nullable(); // PDF labels, CMR, etc.

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
