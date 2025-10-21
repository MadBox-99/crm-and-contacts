<?php

declare(strict_types=1);

use App\Models\Complaint;
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
        Schema::create('complaint_escalations', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Complaint::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'escalated_to')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'escalated_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->timestamp('escalated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_escalations');
    }
};
