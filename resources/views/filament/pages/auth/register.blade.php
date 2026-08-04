<div class="w-full">
    <div class="mb-5">
        <h1 class="text-2xl font-semibold text-gray-900">
            {{ __('Welcome to Sales Module') }}
        </h1>
        <p class="mt-2 text-base text-gray-500">
            {{ __('Start for free - no credit card required.') }}
        </p>
    </div>

    <form wire:submit="register" class="space-y-4">
        {{ $this->form }}

        <div class="pt-1">
            <x-filament::button type="submit" color="primary" class="w-full! justify-center">
                {{ __('Continue') }}
            </x-filament::button>
        </div>

        <p class="text-center text-xs text-gray-500">
            {{ __('By continuing, you agree to our') }}
            <a href="https://cegem360.eu/szolgaltatasi-feltetelek" target="_blank" rel="noopener noreferrer" class="underline" style="color: #EF4444 !important;">{{ __('Terms of Service') }}</a>
            {{ __('and') }}
            <a href="https://cegem360.eu/adatvedelmi-tajekoztato" target="_blank" rel="noopener noreferrer" class="underline" style="color: #EF4444 !important;">{{ __('Privacy Policy') }}</a>.
        </p>

        <p class="text-center text-sm text-gray-500 pt-2">
            {{ __('Already have an account?') }}
            <a href="{{ route('filament.admin.auth.login') }}" class="font-medium" style="color: #EF4444 !important;">
                {{ __('Sign in') }}
            </a>
        </p>
    </form>
</div>
