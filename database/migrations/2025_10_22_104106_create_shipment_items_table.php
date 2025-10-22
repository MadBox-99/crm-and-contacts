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
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignIdFor(App\Models\Shipment::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\OrderItem::class)->nullable()->constrained()->nullOnDelete();

            // External reference (if order_item doesn't exist in CRM)
            $table->string('external_product_id')->nullable()->index();
            $table->string('product_name')->nullable();
            $table->string('product_sku')->nullable();

            // Quantities
            $table->integer('quantity')->default(1);

            // Optional logistics data
            $table->decimal('weight', 10, 2)->nullable(); // kg
            $table->decimal('length', 10, 2)->nullable(); // cm
            $table->decimal('width', 10, 2)->nullable(); // cm
            $table->decimal('height', 10, 2)->nullable(); // cm

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
