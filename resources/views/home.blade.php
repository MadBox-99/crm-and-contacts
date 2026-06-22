<x-layouts.app>
    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-red-50 to-white pt-24 pb-16 lg:pt-32 lg:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-red-100 text-red-700 rounded-full text-sm font-medium mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Sales Module') }}
                </div>

                {{-- H1 --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold text-gray-900 mb-6 font-heading leading-tight">
                    {!! __('Professional :highlight in minutes', ['highlight' => '<span class="text-red-600">' . __('quotes') . '</span>']) !!}
                </h1>

                {{-- Subtitle --}}
                <p class="text-lg sm:text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    {{ __('hero_subtitle') }}
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="https://cegem360.eu/register" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-red-600 rounded-full hover:bg-red-700 transition-colors shadow-lg">
                        {{ __('Get started') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="https://cegem360.eu/kapcsolat" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-red-700 bg-white border-2 border-red-200 rounded-full hover:bg-red-50 transition-colors">
                        {{ __('Request a demo') }}
                    </a>
                    <a href="/login" class="inline-flex items-center justify-center gap-1 px-8 py-4 text-base font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                        {{ __('Log in') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Problem Section --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Do you recognize these problems?') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                {{-- Problem 1 --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Slow quote creation') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('slow_quote_desc') }}
                    </p>
                </div>

                {{-- Problem 2 --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Lost orders') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('lost_orders_desc') }}
                    </p>
                </div>

                {{-- Problem 3 --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Pricing chaos') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('pricing_chaos_desc') }}
                    </p>
                </div>

                {{-- Problem 4 --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('No follow-up') }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ __('no_followup_desc') }}
                    </p>
                </div>
            </div>

            {{-- Solution Box --}}
            <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-8 text-center text-white">
                <h3 class="text-2xl font-semibold mb-4">{{ __('The Cégem360 Sales module solves all of this') }}</h3>
                <p class="text-red-100 max-w-3xl mx-auto">
                    {{ __('solution_desc') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="funkciok" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Everything you need to manage your sales processes') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1: Ajánlatkezelés --}}
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('Quote management') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('quote_mgmt_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Professional quote templates') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Quick quotes from product list') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Quote version tracking') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('PDF export and email') }}
                        </li>
                    </ul>
                </div>

                {{-- Feature 2: Rendeléskezelés --}}
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('Order management') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('order_mgmt_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('One-click order from quote') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Order status tracking') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Partial fulfillment support') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Automatic stock reservation') }}
                        </li>
                    </ul>
                </div>

                {{-- Feature 3: Árazás és kedvezmények --}}
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('Pricing and discounts') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('pricing_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Multi-level price list management') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Customer group discounts') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Volume discounts') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Promotional campaigns') }}
                        </li>
                    </ul>
                </div>

                {{-- Feature 4: Számlázás --}}
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('Invoicing') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('invoicing_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Automatic invoice generation') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('NAV online data reporting') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Partial and final invoicing') }}
                        </li>
                        {{-- Content audit: payment integration hidden until confirmed live --}}
                        @if(false)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Payment tracking') }}
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Feature 5: Automatikus utánkövetés --}}
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('Automatic follow-up') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('followup_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Reminders for open quotes') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Follow-up email sequences') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Quote view notification') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Lost quote analysis') }}
                        </li>
                    </ul>
                </div>

                {{-- Feature 6: Riportok és elemzések --}}
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('Reports and analytics') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('reports_desc') }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Sales dashboard') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Product and customer analysis') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Sales rep performance') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Revenue forecast') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Workflow Section --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('How does it work?') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Step 1 --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            1
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Create quote') }}</h3>
                        <p class="text-gray-600 text-sm">
                            {{ __('create_quote_desc') }}
                        </p>
                    </div>
                    <div class="hidden lg:block absolute top-1/2 -right-3 transform -translate-y-1/2 text-red-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            2
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Send and track') }}</h3>
                        <p class="text-gray-600 text-sm">
                            {{ __('send_track_desc') }}
                        </p>
                    </div>
                    <div class="hidden lg:block absolute top-1/2 -right-3 transform -translate-y-1/2 text-red-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            3
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Follow-up') }}</h3>
                        <p class="text-gray-600 text-sm">
                            {{ __('followup_step_desc') }}
                        </p>
                    </div>
                    <div class="hidden lg:block absolute top-1/2 -right-3 transform -translate-y-1/2 text-red-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            4
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Order') }}</h3>
                        <p class="text-gray-600 text-sm">
                            {{ __('order_step_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Results Section --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Results in numbers') }}
                </h2>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-red-50 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">-70%</div>
                    <div class="text-sm text-gray-600">{{ __('Quote creation time') }}</div>
                </div>
                <div class="bg-red-50 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">+35%</div>
                    <div class="text-sm text-gray-600">{{ __('Quote conversion') }}</div>
                </div>
                <div class="bg-red-50 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">+20%</div>
                    <div class="text-sm text-gray-600">{{ __('Average deal size') }}</div>
                </div>
                <div class="bg-red-50 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">+80%</div>
                    <div class="text-sm text-gray-600">{{ __('Follow-up rate') }}</div>
                </div>
                <div class="col-span-2 lg:col-span-1 bg-red-50 rounded-2xl p-6 text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">-50%</div>
                    <div class="text-sm text-gray-600">{{ __('Administrative work') }}</div>
                </div>
            </div>
            <p class="mt-8 text-center text-xs text-gray-400">* {{ __('Illustrative examples') }}</p>
        </div>
    </section>

    {{-- Integrations Section --}}
    <section id="integraciok" class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Connect to your existing tools') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Számlázás --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ __('Billing') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Billingo, Számlázz.hu, NAV</p>
                </div>

                {{-- Content audit: webshop / courier / payment integrations hidden until confirmed live --}}
                @if(false)
                {{-- Webshopok --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ __('Webshops') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">WooCommerce, Shopify, Shoprenter</p>
                </div>

                {{-- Futárszolgálatok --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ __('Couriers') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">GLS, DPD, FoxPost, MPL</p>
                </div>

                {{-- Fizetés --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ __('Payment') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Barion, SimplePay, OTP</p>
                </div>
                @endif

                {{-- Könyvelőprogram --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ __('Accounting software') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Kulcs-Soft, Novitax, RLB</p>
                </div>

                {{-- Nyomtatás --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ __('Printing') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">{{ __('Label printers, POS printers') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Who Uses Section --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Who uses it?') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('Sales reps') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('sales_reps_desc') }}</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('Billing') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('billing_desc') }}</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('Logistics') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('logistics_desc') }}</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('Managers') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('managers_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    @if(false)
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('What do our customers say?') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Testimonial 1 --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-1 mb-4">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">
                        "{{ __('testimonial_1') }}"
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-red-600 font-semibold">VL</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('testimonial_1_name') }}</div>
                            <div class="text-sm text-gray-600">{{ __('testimonial_1_title') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-1 mb-4">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">
                        "{{ __('testimonial_2') }}"
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-red-600 font-semibold">KL</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('testimonial_2_name') }}</div>
                            <div class="text-sm text-gray-600">{{ __('testimonial_2_title') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Pricing Section --}}
    @if(false)
    <section id="arak" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Choose the right package for your company') }}
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Starter Tier --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Starter') }}</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-gray-900">4 900 Ft</span>
                        <span class="text-gray-500">{{ __('per user/month') }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-6">{{ __('For small teams') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            500 {{ __('quotes per month') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Basic quote templates') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Email sending') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Basic reports') }}
                        </li>
                    </ul>
                    <a href="https://cegem360.eu/register" class="block w-full text-center py-3 px-6 rounded-full text-sm font-medium border-2 border-red-200 text-red-600 hover:bg-red-50 transition-colors">
                        {{ __('Get started') }}
                    </a>
                </div>

                {{-- Professional (Featured) --}}
                <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-red-500 relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            {{ __('Most popular') }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Professional') }}</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-gray-900">9 900 Ft</span>
                        <span class="text-gray-500">{{ __('per user/month') }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-6">{{ __('For growing sales teams') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Unlimited quotes') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Custom templates') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Automatic follow-up') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Advanced reports') }}
                        </li>
                    </ul>
                    <a href="https://cegem360.eu/register" class="block w-full py-3 text-center text-sm font-medium text-white bg-red-600 rounded-full hover:bg-red-700 transition-colors">
                        {{ __('Get started') }}
                    </a>
                </div>

                {{-- Enterprise --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Enterprise') }}</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-gray-900">{{ __('Custom') }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-6">{{ __('For enterprise needs') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Dedicated support') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Custom integrations') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('SLA guarantee') }}
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('On-premise option') }}
                        </li>
                    </ul>
                    <a href="https://cegem360.eu/kapcsolat" class="block w-full py-3 text-center text-sm font-medium text-gray-700 border-2 border-gray-200 rounded-full hover:bg-gray-50 transition-colors">
                        {{ __('Request quote') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Related Modules Section --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Related modules') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                {{-- CRM modul --}}
                <a href="https://crm.cegem360.eu" class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ __('CRM module') }}</h3>
                    <p class="text-gray-600">
                        {{ __('crm_desc') }}
                    </p>
                </a>

                {{-- Beszerzés-logisztika modul --}}
                <a href="https://beszerzes.cegem360.eu" class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-amber-600 transition-colors">{{ __('Procurement-logistics module') }}</h3>
                    <p class="text-gray-600">
                        {{ __('procurement_desc') }}
                    </p>
                </a>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section id="gyik" class="py-16 lg:py-24 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 mb-4 font-heading">
                    {{ __('Frequently asked questions') }}
                </h2>
            </div>

            <div class="space-y-4" x-data="{ openFaq: null }">
                {{-- FAQ 1 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_1_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_1_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_2_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_2_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_3_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_3_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_4_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_4_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 5 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_5_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 5" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_5_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 6 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_6_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 6 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 6" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_6_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 7 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 7 ? null : 7" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_7_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 7 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 7" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_7_a') }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 8 --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200">
                    <button @click="openFaq = openFaq === 8 ? null : 8" class="w-full px-6 py-4 flex justify-between items-center text-left">
                        <span class="font-medium text-gray-900">{{ __('faq_8_q') }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 8 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="openFaq === 8" x-collapse>
                        <div class="px-6 pb-4 text-gray-600">
                            {{ __('faq_8_a') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA Section --}}
    <section id="kapcsolat" class="py-16 lg:py-24 bg-gradient-to-r from-red-600 to-orange-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-semibold text-white mb-4 font-heading">
                {{ __('Ready for professional sales?') }}
            </h2>
            <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto">
                {{ __('Discover how Cégem360 helps your business grow. No long-term commitment.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://cegem360.eu/register" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-red-600 bg-white rounded-full hover:bg-gray-100 transition-colors">
                    {{ __('Get started') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="https://cegem360.eu/kapcsolat" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white border-2 border-white/30 rounded-full hover:bg-white/10 transition-colors">
                    {{ __('Request a demo') }}
                </a>
                <a href="/login" class="inline-flex items-center justify-center gap-1 px-8 py-4 text-base font-semibold text-white/80 hover:text-white transition-colors">
                    {{ __('Log in') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    {{-- Content audit: legal disclaimer + central cegem360.eu legal links --}}
    <section class="border-t border-gray-200 bg-white py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gray-50 p-6 text-sm leading-relaxed text-gray-500 sm:p-8">
                <p class="mb-3 text-base font-semibold text-gray-700">{{ __('Legal information') }}</p>
                <div class="space-y-2">
                    <p>{{ __('Invoicing complies with the applicable VAT Act and NAV (Hungarian Tax Authority) requirements; the user is responsible for the accuracy of the data content.') }}</p>
                    <p>{{ __('The module can be connected to the Cégem360 Invoicing module.') }}</p>
                    <p>{{ __('The figures and results shown on this page are illustrative examples and not guaranteed.') }}</p>
                </div>
                <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2">
                    <a href="https://cegem360.eu/adatvedelmi-tajekoztato" class="font-medium text-gray-700 underline underline-offset-2 hover:text-gray-900">{{ __('Privacy notice') }}</a>
                    <a href="https://cegem360.eu/adatfeldolgozoi-tajekoztato" class="font-medium text-gray-700 underline underline-offset-2 hover:text-gray-900">{{ __('Data Processing Agreement') }}</a>
                    <a href="https://cegem360.eu/szolgaltatasi-feltetelek" class="font-medium text-gray-700 underline underline-offset-2 hover:text-gray-900">{{ __('Terms of service') }}</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-gray-200" x-data="{ openSection: null }">
        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-8 gap-y-8">
                <!-- Column 1: Logo + Links -->
                <div class="col-span-2 md:col-span-3 lg:col-span-1">
                    <a href="/" class="flex items-center gap-2 mb-5">
                        <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10">
                        <span class="text-sm font-semibold text-red-600">{{ __('Sales') }}</span>
                    </a>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('footer_desc') }}
                    </p>
                    <ul class="space-y-2.5 text-sm text-gray-700">
                        <li><a href="#arak" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Pricing') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Contact') }}</a></li>
                        <li><a href="#gyik" class="text-inherit! hover:text-red-600! transition-colors">{{ __('FAQ') }}</a></li>
                    </ul>
                </div>

                <!-- Column 2: Modules -->
                <div class="col-span-1">
                    <button
                        class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                        @click="openSection = openSection === 'modulok' ? null : 'modulok'"
                    >
                        {{ __('Modules') }}
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'modulok' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">{{ __('Modules') }}</h3>
                    <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'modulok' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('CRM') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Controlling') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Sales') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Procurement') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Manufacturing') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Automation') }}</a></li>
                    </ul>
                </div>

                <!-- Column 3: Company -->
                <div class="col-span-1">
                    <button
                        class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                        @click="openSection = openSection === 'ceg' ? null : 'ceg'"
                    >
                        {{ __('Company') }}
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'ceg' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">{{ __('Company') }}</h3>
                    <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'ceg' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('About us') }}</a></li>
                        <li><a href="#arak" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Pricing') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('References') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Custom solutions') }}</a></li>
                    </ul>
                </div>

                <!-- Column 4: Help -->
                <div class="col-span-1">
                    <button
                        class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                        @click="openSection = openSection === 'segitseg' ? null : 'segitseg'"
                    >
                        {{ __('Help') }}
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'segitseg' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">{{ __('Help') }}</h3>
                    <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'segitseg' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Help center') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Knowledge base') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Contact') }}</a></li>
                        <li><a href="#gyik" class="text-inherit! hover:text-red-600! transition-colors">{{ __('FAQ') }}</a></li>
                    </ul>
                </div>

                <!-- Column 5: Cégem360 -->
                <div class="col-span-1">
                    <button
                        class="lg:hidden w-full flex items-center justify-between text-[15px] font-semibold text-gray-900 mb-4"
                        @click="openSection = openSection === 'cegem360' ? null : 'cegem360'"
                    >
                        Cégem360
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openSection === 'cegem360' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <h3 class="hidden lg:block text-[15px] font-semibold text-gray-900 mb-4">Cégem360</h3>
                    <ul class="space-y-2.5 text-sm text-gray-700" x-show="openSection === 'cegem360' || window.innerWidth >= 1024" x-collapse.duration.300ms>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Home') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Modules') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('About us') }}</a></li>
                        <li><a href="#" class="text-inherit! hover:text-red-600! transition-colors">{{ __('Blog') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sub-footer -->
        <div class="border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-5">
                    <!-- Left: Social icons -->
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <div class="flex items-center gap-3">
                            <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom row: Legal links -->
                <div class="mt-5 pt-5 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-gray-500">
                        <span class="text-gray-700">{{ __('Cégem360 Sales is a product of Cégem360 Kft.') }}</span>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-gray-500">
                        <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Terms of Service') }}</a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Privacy Policy') }}</a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="hover:text-gray-700 transition-colors">{{ __('Cookie Policy') }}</a>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="mt-3 text-center sm:text-left text-sm text-gray-400">
                    {{ __('All rights reserved.') }} &copy; {{ date('Y') }} Cégem360
                </div>
            </div>
        </div>
    </footer>
</x-layouts.app>
