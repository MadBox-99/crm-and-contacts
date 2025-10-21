<x-layouts.app>

    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Chat Support Demo
                    </h1>
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            Logged in as: <span class="font-semibold">{{ $customer->name }}</span>
                        </div>
                        <a href="/"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
                            Welcome to Live Chat Support
                        </h2>

                        <div class="prose max-w-none text-gray-600 dark:text-gray-300 space-y-4">
                            <p>
                                This is a demonstration of the real-time chat system. Click the chat button
                                in the bottom-right corner to start a conversation with our support team.
                            </p>

                            <div class="grid md:grid-cols-2 gap-6 my-8">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-blue-900">Real-time Communication</h3>
                                    </div>
                                    <p class="text-sm text-blue-800">
                                        Messages are delivered instantly using WebSocket technology powered by
                                        Laravel Reverb.
                                    </p>
                                </div>

                                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-green-900">Read Receipts</h3>
                                    </div>
                                    <p class="text-sm text-green-800">
                                        See when your messages have been read by support agents with blue
                                        checkmarks.
                                    </p>
                                </div>

                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-purple-900">Typing Indicators</h3>
                                    </div>
                                    <p class="text-sm text-purple-800">
                                        Know when support agents are composing a response to your message.
                                    </p>
                                </div>

                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-orange-900">Session History</h3>
                                    </div>
                                    <p class="text-sm text-orange-800">
                                        Your chat history is saved. Return anytime to continue your conversation.
                                    </p>
                                </div>
                            </div>

                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">How to Use:</h3>
                                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                    <li>Click the blue chat button in the bottom-right corner</li>
                                    <li>Type your message in the input field</li>
                                    <li>Press Enter or click the send button</li>
                                    <li>Wait for a support agent to respond (in this demo, you can open the admin
                                        panel to respond yourself)</li>
                                </ol>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-900">Demo Mode</p>
                                        <p class="text-sm text-yellow-800 mt-1">
                                            To test the admin side, log in to the <a href="/admin"
                                                class="underline font-medium">Filament Admin Panel</a>
                                            and navigate to Chat Sessions to respond to your messages.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Chat Widget -->
    <livewire:chat.chat-widget :customerId="$customer->id" />

</x-layouts.app>
