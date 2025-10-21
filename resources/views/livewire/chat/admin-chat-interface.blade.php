<div class="flex h-[calc(100vh-12rem)] flex-col bg-white dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden"
    wire:poll.15s="refreshMessages">
    {{-- Chat Header --}}
    <div
        class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 text-white">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-xl font-bold">
                    {{ strtoupper(substr($session->customer->name ?? 'U', 0, 1)) }}
                </div>
            </div>
            <div>
                <h2 class="text-lg font-semibold">{{ $session->customer->name }}</h2>
                <p class="text-sm text-blue-100">
                    {{ $session->customer->email }}
                    @if ($session->status->value === 'active')
                        <span class="ml-2 inline-flex items-center">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-1"></span>
                            Active
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @if ($session->user)
                <div class="text-right">
                    <p class="text-sm font-medium">Assigned to</p>
                    <p class="text-xs text-blue-100">{{ $session->user->name }}</p>
                </div>
            @endif

            <button wire:click="markAllAsRead"
                class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-sm font-medium"
                title="Mark all as read">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Chat Messages --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50 dark:bg-gray-800"
        x-data="{
            scrollToBottom() {
                this.$el.scrollTop = this.$el.scrollHeight;
            }
        }" x-init="scrollToBottom()" @scroll-to-bottom.window="scrollToBottom()">

        @forelse($session->messages as $msg)
            <div class="flex {{ $msg->sender_type->value === 'user' ? 'justify-end' : 'justify-start' }}"
                wire:key="message-{{ $msg->id }}">
                <div
                    class="flex items-end space-x-2 max-w-2xl {{ $msg->sender_type->value === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                    {{-- Avatar --}}
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full {{ $msg->sender_type->value === 'user' ? 'bg-blue-600' : 'bg-gray-400' }} flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($msg->sender->name ?? 'U', 0, 1)) }}
                    </div>

                    {{-- Message Bubble --}}
                    <div class="flex flex-col {{ $msg->sender_type->value === 'user' ? 'items-end' : 'items-start' }}">
                        <div
                            class="px-4 py-3 rounded-2xl {{ $msg->sender_type->value === 'user'
                                ? 'bg-blue-600 text-white rounded-br-none'
                                : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-bl-none shadow-md' }}">
                            <p class="text-sm whitespace-pre-wrap break-words">{{ $msg->message }}</p>
                        </div>
                        <div class="flex items-center space-x-2 mt-1 px-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $msg->created_at->format('H:i') }}
                            </span>
                            @if ($msg->sender_type->value === 'user')
                                <span class="text-xs">
                                    @if ($msg->is_read)
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No messages yet</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Start the conversation below</p>
            </div>
        @endforelse
    </div>

    {{-- Message Input --}}
    <div class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <form wire:submit="sendMessage" class="flex items-end space-x-3">
            <div class="flex-1">
                <textarea wire:model.live.debounce.300ms="message" rows="2" placeholder="Type your message..."
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                    @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); }" @keydown.shift.enter=""></textarea>
                @error('message')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                wire:loading.attr="disabled" wire:target="sendMessage">
                <span wire:loading.remove wire:target="sendMessage">Send</span>
                <span wire:loading wire:target="sendMessage">Sending...</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>

        <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>Press Enter to send, Shift+Enter for new line</span>
            <div class="flex items-center space-x-4">
                @if ($session->unread_count > 0)
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        {{ $session->unread_count }} unread
                    </span>
                @endif
                <span>{{ $session->messages->count() }} messages</span>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        // Initialize Echo listener for this chat session
        const sessionId = {{ $session->id }};

        console.log('Initializing Echo listener for session:', sessionId);

        if (window.Echo) {
            console.log('Echo is available, subscribing to private channel...');

            window.Echo.private(`chat.session.${sessionId}`)
                .listen('.message.sent', (e) => {
                    console.log('New message received via Echo:', e);
                    // Livewire will handle the refresh via the #[On] attribute
                })
                .error((error) => {
                    console.error('Echo subscription error:', error);
                });
        } else {
            console.warn('Echo is not available, falling back to polling');
        }

        // Scroll to bottom when component is loaded
        document.addEventListener('livewire:navigated', () => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });

        // Listen for new messages and scroll-to-bottom events
        window.addEventListener('message-sent', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        });

        window.addEventListener('scroll-to-bottom', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        });
    </script>
@endscript
