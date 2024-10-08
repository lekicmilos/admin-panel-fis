<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    {{-- Flatpickr  --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr.setDefaults({
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
            });
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            FIS
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <div class="font-bold text-3xl p-5 pt-3">FIS</div>

            {{-- MENU --}}
            <x-menu activate-by-route>

                <x-menu-item title="Zvanje" link="/zvanje" />
                <x-menu-item title="Zaposleni" link="/zaposleni" />
                <x-menu-item title="Katedre" link="/katedra" />
                <x-theme-toggle class="btn"/>

            </x-menu>
{{--             Theme toggle --}}
            <x-theme-toggle class="hidden" />
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />

</body>
</html>


