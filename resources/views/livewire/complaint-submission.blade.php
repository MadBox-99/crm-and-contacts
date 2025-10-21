<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        @if($submitted)
            {{-- Success Message --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-6">
                    <svg class="h-10 w-10 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Complaint Submitted Successfully!</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                    Thank you for bringing this to our attention. Our team will review your complaint and get back to you as soon as possible.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                    You will receive a confirmation email shortly.
                </p>
                <button
                    wire:click="$set('submitted', false)"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Submit Another Complaint
                </button>
            </div>
        @else
            {{-- Complaint Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-red-600 to-red-700 dark:from-red-700 dark:to-red-800 px-8 py-6">
                    <h1 class="text-3xl font-bold text-white">Submit a Complaint</h1>
                    <p class="mt-2 text-red-100">We take your concerns seriously and will address them promptly.</p>
                </div>

                {{-- Form --}}
                <form wire:submit="submit" class="px-8 py-6 space-y-6">
                    {{-- Personal Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                            Your Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    wire:model="name"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                    placeholder="John Doe">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                    placeholder="john@example.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    wire:model="phone"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                    placeholder="+36 20 123 4567">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Order Number --}}
                            <div>
                                <label for="order_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Order Number (if applicable)
                                </label>
                                <input
                                    type="text"
                                    id="order_number"
                                    wire:model="order_number"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                    placeholder="ORD-12345">
                                @error('order_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Complaint Details Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                            Complaint Details
                        </h3>

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                wire:model="title"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                placeholder="Brief summary of your complaint">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="description"
                                wire:model="description"
                                rows="6"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-none"
                                placeholder="Please provide detailed information about your complaint..."></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum 10 characters</p>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="text-red-500">*</span> Required fields
                        </p>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span wire:loading.remove wire:target="submit">Submit Complaint</span>
                            <span wire:loading wire:target="submit">Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">What happens next?</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                            <ul class="list-disc list-inside space-y-1">
                                <li>You'll receive a confirmation email</li>
                                <li>Our team will review your complaint within 24 hours</li>
                                <li>An agent will be assigned to your case</li>
                                <li>You'll be contacted with an update or resolution</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
