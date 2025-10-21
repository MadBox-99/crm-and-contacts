<div>
    {{-- Chat Toggle Button (Floating) --}}
    @if (!$isOpen)
        <button
            wire:click="toggleChat"
            type="button"
            class="fixed bottom-4 right-4 z-50 flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg transition-all duration-300 hover:scale-110"
            aria-label="Open chat"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            @if ($session && $session->unread_count > 0)
                <span class="absolute -top-1 -right-1 flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-600 rounded-full">
                    {{ $session->unread_count }}
                </span>
            @endif
        </button>
    @endif

    {{-- Chat Window --}}
    @if ($isOpen)
        <div class="fixed bottom-4 right-4 z-50 w-96 max-w-full {{ $isMinimized ? 'h-16' : 'h-[600px]' }} bg-white rounded-lg shadow-2xl flex flex-col transition-all duration-300">
            {{-- Header --}}
            <div class="flex items-center justify-between p-4 bg-blue-600 text-white rounded-t-lg">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <h3 class="font-semibold">Chat Support</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="minimizeChat"
                        type="button"
                        class="p-1 hover:bg-blue-700 rounded transition-colors"
                        aria-label="Minimize chat"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <button
                        wire:click="closeChat"
                        type="button"
                        class="p-1 hover:bg-blue-700 rounded transition-colors"
                        aria-label="Close chat"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Chat Content --}}
            @if (!$isMinimized)
                @if ($session)
                    <div class="flex-1 flex flex-col overflow-hidden">
                        {{-- Messages --}}
                        <livewire:chat.chat-message-list :session="$session" :key="'messages-'.$session->id" />

                        {{-- Input --}}
                        <livewire:chat.chat-message-input :session="$session" :customer="$customer" :key="'input-'.$session->id" />
                    </div>
                @else
                    {{-- Welcome Screen --}}
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Welcome to Chat Support</h4>
                        <p class="text-sm text-gray-600 mb-6">We're here to help! Start a conversation with our team.</p>
                        <button
                            wire:click="startChat"
                            type="button"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                        >
                            Start Chat
                        </button>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
