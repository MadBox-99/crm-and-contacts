<?php

declare(strict_types=1);

use App\Models\ChatSession;
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
        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(ChatSession::class)->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['customer', 'user', 'bot'])->default('customer');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false)->after('message');
            $table->timestamp('read_at')->nullable()->after('is_read');
            $table->unsignedBigInteger('parent_message_id')->nullable()->after('chat_session_id');
            $table->timestamp('edited_at')->nullable()->after('read_at');
            $table->softDeletes();

            $table->foreign('parent_message_id')
                ->references('id')
                ->on('chat_messages')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
