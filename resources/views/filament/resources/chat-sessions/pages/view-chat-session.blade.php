<x-filament-panels::page>
    @vite('resources/js/app.js')
    <div class="space-y-6">
        {{-- Session Info Card --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $record->status}}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ ucfirst($record->priority) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Priority</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $record->unread_count }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Unread Messages</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $record->messages->count() }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Messages</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Live Chat Interface --}}
        <x-filament::section>
            @livewire('chat.admin-chat-interface', ['session' => $record])
        </x-filament::section>

        {{-- Session Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-filament::section>
                <x-slot name="heading">
                    Session Information
                </x-slot>

                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Started At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $record->started_at->format('M d, Y H:i:s') }}
                            <span class="text-gray-500 dark:text-gray-400">
                                ({{ $record->started_at->diffForHumans() }})
                            </span>
                        </dd>
                    </div>

                    @if($record->ended_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ended At</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $record->ended_at->format('M d, Y H:i:s') }}
                                <span class="text-gray-500 dark:text-gray-400">
                                    ({{ $record->ended_at->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                    @endif

                    @if($record->last_message_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Message</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $record->last_message_at->format('M d, Y H:i:s') }}
                                <span class="text-gray-500 dark:text-gray-400">
                                    ({{ $record->last_message_at->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                    @endif

                    @if($record->rating)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Rating</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ str_repeat('⭐', $record->rating) }} ({{ $record->rating }}/5)
                            </dd>
                        </div>
                    @endif
                </dl>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Participants
                </x-slot>

                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            <div class="font-semibold">{{ $record->customer->name }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ $record->customer->email }}</div>
                            @if($record->customer->phone)
                                <div class="text-gray-500 dark:text-gray-400">{{ $record->customer->phone }}</div>
                            @endif
                        </dd>
                    </div>

                    @if($record->user)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Agent</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <div class="font-semibold">{{ $record->user->name }}</div>
                                <div class="text-gray-500 dark:text-gray-400">{{ $record->user->email }}</div>
                            </dd>
                        </div>
                    @else
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Agent</dt>
                            <dd class="mt-1 text-sm text-orange-600 dark:text-orange-400">
                                Unassigned
                            </dd>
                        </div>
                    @endif
                </dl>
            </x-filament::section>
        </div>

        {{-- Notes --}}
        @if($record->notes)
            <x-filament::section>
                <x-slot name="heading">
                    Internal Notes
                </x-slot>

                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $record->notes }}</p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
