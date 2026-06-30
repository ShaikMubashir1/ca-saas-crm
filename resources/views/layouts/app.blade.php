<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CA SaaS CRM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex h-screen bg-gray-50 overflow-hidden">
            <!-- Sidebar -->
            <livewire:layout.sidebar />

            <!-- Main Content -->
            <div class="flex-1 flex flex-col w-0 overflow-hidden">
                <!-- Header -->
                <livewire:layout.header />

                <!-- Page Content -->
                <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
