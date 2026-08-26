<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Standard Touch e-Solutions CRM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-[#F7F7F8] text-[#252525] selection:bg-[#ED1C24] selection:text-white" x-data="{ sidebarOpen: false }">
        <div class="min-h-full flex h-screen overflow-hidden bg-[#F7F7F8]">
            
            <!-- Mobile Sidebar Backdrop & Menu -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 flex md:hidden" role="dialog" aria-modal="true" style="display: none;">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs" @click="sidebarOpen = false"></div>
                
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-[#252525]">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <livewire:layout.sidebar />
                </div>
            </div>

            <!-- Desktop Sidebar -->
            <div class="hidden md:flex md:flex-shrink-0">
                <livewire:layout.sidebar />
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col w-0 overflow-hidden bg-[#F7F7F8]">
                <!-- Header -->
                <livewire:layout.header />

                <!-- Page Content -->
                <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-[1340px] mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
