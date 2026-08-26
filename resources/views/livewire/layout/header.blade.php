<header class="relative z-10 flex-shrink-0 flex h-16 bg-white border-b border-[#E5E5E5] shadow-xs">
    {{-- Mobile Hamburger Button --}}
    <button type="button" @click="sidebarOpen = true" class="px-4 text-slate-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#ED1C24] md:hidden">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div class="flex-1 px-4 sm:px-6 flex justify-between items-center">
        {{-- Search Input (Max width 550px) --}}
        <div class="flex-1 flex max-w-[520px]">
            <div class="relative w-full text-slate-400 focus-within:text-[#ED1C24]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input id="search-field" class="block w-full pl-9 pr-4 py-2 text-xs bg-[#F7F7F8] border border-[#E5E5E5] rounded-lg text-[#252525] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#ED1C24]/20 focus:border-[#ED1C24] focus:bg-white transition-all duration-150" placeholder="Search clients, tasks, or documents..." type="search">
            </div>
        </div>

        {{-- Right Controls: Notifications & User Avatar --}}
        <div class="ml-4 flex items-center space-x-3">
            {{-- Notification Bell --}}
            <button class="relative p-2 rounded-lg text-slate-500 hover:text-[#252525] hover:bg-[#F7F7F8] focus:outline-none focus:ring-2 focus:ring-[#ED1C24] transition-colors">
                <span class="sr-only">View notifications</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-[#ED1C24] ring-2 ring-white"></span>
            </button>

            <div class="h-5 w-px bg-[#E5E5E5]"></div>

            {{-- User Menu Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2.5 text-xs font-semibold text-[#252525] rounded-lg p-1 hover:bg-[#F7F7F8] focus:outline-none transition-colors" id="user-menu" aria-haspopup="true">
                    <div class="h-8 w-8 rounded-lg bg-[#ED1C24] flex items-center justify-center text-white font-bold text-xs shadow-sm">
                        {{ substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                    <span class="hidden md:inline-block font-semibold text-[#252525]">{{ auth()->user()->name ?? 'User' }}</span>
                    <svg class="hidden md:inline-block h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="origin-top-right absolute right-0 mt-2 w-52 rounded-xl shadow-xl py-1 bg-white border border-[#E5E5E5] divide-y divide-slate-100 z-50" role="menu" style="display: none;">
                    <div class="px-4 py-2.5">
                        <p class="text-xs font-bold text-[#252525] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-[#737373] truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-xs font-medium text-[#252525] hover:bg-[#F7F7F8]" role="menuitem" wire:navigate>Your Profile</a>
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-xs font-medium text-[#252525] hover:bg-[#F7F7F8]" role="menuitem" wire:navigate>Account Settings</a>
                    </div>
                    <div class="py-1">
                        <button wire:click="logout" class="block w-full text-left px-4 py-2 text-xs font-semibold text-[#ED1C24] hover:bg-red-50" role="menuitem">Sign out</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
