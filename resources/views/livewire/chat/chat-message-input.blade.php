<div class="border-t border-gray-200 p-4 bg-white rounded-b-lg">
    <form wire:submit="sendMessage" class="flex flex-col gap-2">
        {{-- Typing Indicator --}}
        <div wire:loading wire:target="sendMessage" class="text-xs text-gray-500 flex items-center gap-1">
            <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span>Sending...</span>
        </div>

        {{-- Input Area --}}
        <div class="flex gap-2">
            <textarea wire:model.live.debounce.50ms="message" rows="1" placeholder="Type your message..."
                class="flex-1 resize-none border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); }"></textarea>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!$wire.message.trim()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>

        @error('message')
            <span class="text-xs text-red-600">{{ $message }}</span>
        @enderror
    </form>
</div>
