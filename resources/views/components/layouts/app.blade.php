<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ config('app.name') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        <script>
            // Apply theme before page renders to prevent flash
            (function() {
                const theme = localStorage.getItem('theme') || 'auto';
                const root = document.documentElement;

                if (theme === 'dark') {
                    root.classList.add('dark');
                } else if (theme === 'light') {
                    root.classList.remove('dark');
                } else { // auto
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        root.classList.add('dark');
                    } else {
                        root.classList.remove('dark');
                    }
                }
            })();
        </script>
        @filamentStyles

        @vite('resources/js/app.js')

    </head>

    <body class="antialiased" x-data="{ mobileMenuOpen: false }">
        <x-layouts.navbar />

        {{ $slot }}

        @livewire('notifications')

        @filamentScripts

    </body>

</html>
