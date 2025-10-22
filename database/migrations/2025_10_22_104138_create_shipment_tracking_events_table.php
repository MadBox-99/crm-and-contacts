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
        Schema::create('shipment_tracking_events', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignIdFor(App\Models\Shipment::class)->constrained()->cascadeOnDelete();

            // Event details
            $table->string('status_code'); // PICKED_UP, IN_TRANSIT, OUT_FOR_DELIVERY, DELIVERED, etc.
            $table->string('location')->nullable(); // City/Country
            $table->text('description')->nullable(); // Human-readable description
            $table->timestamp('occurred_at'); // When this event happened (from external system)

            // Additional data from external system
            $table->json('metadata')->nullable(); // Any extra data

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_events');
    }
};
