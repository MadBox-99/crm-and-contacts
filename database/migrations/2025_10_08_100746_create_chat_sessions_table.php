<?php

declare(strict_types=1);

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
        Schema::create('chat_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Customer::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['active', 'closed', 'transferred'])->default('active');
            $table->timestamp('last_message_at')->nullable()->after('ended_at');
            $table->unsignedInteger('unread_count')->default(0)->after('last_message_at');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('status');
            $table->tinyInteger('rating')->nullable()->after('priority');
            $table->text('notes')->nullable()->after('rating');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
