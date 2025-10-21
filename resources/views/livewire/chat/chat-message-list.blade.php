<div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chat-messages" wire:poll.5s="refreshMessages">
    @forelse ($messages as $message)
        <div class="flex {{ $message->sender_type->value === 'customer' ? 'justify-end' : 'justify-start' }}">
            <div
                class="flex gap-2 max-w-[80%] {{ $message->sender_type->value === 'customer' ? 'flex-row-reverse' : 'flex-row' }}">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <div
                        class="w-8 h-8 rounded-full {{ $message->sender_type->value === 'customer' ? 'bg-blue-600' : 'bg-gray-400' }} flex items-center justify-center text-white text-sm font-medium">
                        {{ substr($message->sender?->name ?? 'U', 0, 1) }}
                    </div>
                </div>

                {{-- Message Bubble --}}
                <div
                    class="flex flex-col {{ $message->sender_type->value === 'customer' ? 'items-end' : 'items-start' }}">
                    <div
                        class="px-4 py-2 rounded-lg {{ $message->sender_type->value === 'customer' ? 'bg-blue-600 text-white' : 'bg-white text-gray-900' }} shadow-sm">
                        <p class="text-sm whitespace-pre-wrap break-words">{{ $message->message }}</p>
                    </div>
                    <div class="flex items-center gap-2 mt-1 px-2">
                        <span class="text-xs text-gray-500">
                            {{ $message->created_at?->format('H:i') }}
                        </span>
                        @if ($message->sender_type->value === 'customer' && $message->is_read)
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex items-center justify-center h-full text-gray-500">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <p class="text-sm">No messages yet</p>
                <p class="text-xs mt-1">Send a message to start the conversation</p>
            </div>
        </div>
    @endforelse
</div>

@script
    <script>
        // Auto-scroll to bottom when new messages arrive
        $wire.on('scroll-to-bottom', () => {
            const messagesContainer = document.getElementById('chat-messages');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });

        // Scroll to bottom on initial load
        document.addEventListener('DOMContentLoaded', () => {
            const messagesContainer = document.getElementById('chat-messages');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    </script>
@endscript
