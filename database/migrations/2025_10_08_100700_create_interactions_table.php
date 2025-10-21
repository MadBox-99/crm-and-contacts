<?php

declare(strict_types=1);

use App\Enums\InteractionType;
use App\Models\Customer;
use App\Models\User;
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
        Schema::create('interactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('type')->default(InteractionType::Note->value);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->timestamp('interaction_date');
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->string('next_action')->nullable();
            $table->date('next_action_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
