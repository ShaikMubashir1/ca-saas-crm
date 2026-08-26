<aside class="flex flex-col h-full w-64 min-w-[250px] bg-[#252525] text-slate-300 border-r border-[#1E1E1E] shadow-2xl shrink-0">
    {{-- Branded Top Logo Area --}}
    <div class="flex items-center flex-shrink-0 px-5 h-16 bg-[#1E1E1E] border-b border-[#2D2D2D]">
        <a href="{{ route('dashboard') }}" class="flex items-center group" wire:navigate>
            <div class="flex flex-col">
                <div class="flex items-center text-lg font-black tracking-tight leading-none">
                    <span class="text-[#ED1C24]">Standard</span>
                    <span class="text-white ml-1">Touch</span>
                </div>
                <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase italic mt-0.5">e-Solutions</span>
            </div>
        </a>
    </div>

    {{-- Navigation List --}}
    <div class="flex-1 flex flex-col min-h-0 py-5 overflow-y-auto px-3.5 space-y-6">
        {{-- MAIN SECTION --}}
        <div>
            <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2.5">Main Menu</p>
            <nav class="space-y-1">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" 
                   class="{{ request()->routeIs('dashboard') ? 'bg-[#ED1C24] text-white font-semibold shadow-md shadow-red-900/20' : 'text-slate-400 hover:bg-[#2D2D2D] hover:text-white' }} group flex items-center px-3 py-2.5 text-xs rounded-lg transition-all duration-150" 
                   wire:navigate>
                    <svg class="mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    Dashboard
                </a>

                {{-- Clients --}}
                <a href="{{ route('clients.index') }}" 
                   class="{{ request()->routeIs('clients.*') ? 'bg-[#ED1C24] text-white font-semibold shadow-md shadow-red-900/20' : 'text-slate-400 hover:bg-[#2D2D2D] hover:text-white' }} group flex items-center px-3 py-2.5 text-xs rounded-lg transition-all duration-150" 
                   wire:navigate>
                    <svg class="mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Clients
                </a>

                {{-- Tasks --}}
                <a href="{{ route('tasks.index') }}" 
                   class="{{ request()->routeIs('tasks.*') ? 'bg-[#ED1C24] text-white font-semibold shadow-md shadow-red-900/20' : 'text-slate-400 hover:bg-[#2D2D2D] hover:text-white' }} group flex items-center px-3 py-2.5 text-xs rounded-lg transition-all duration-150" 
                   wire:navigate>
                    <svg class="mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('tasks.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    Tasks & Compliance
                </a>

                {{-- Invoices --}}
                <a href="{{ route('invoices.index') }}" 
                   class="{{ request()->routeIs('invoices.*') ? 'bg-[#ED1C24] text-white font-semibold shadow-md shadow-red-900/20' : 'text-slate-400 hover:bg-[#2D2D2D] hover:text-white' }} group flex items-center px-3 py-2.5 text-xs rounded-lg transition-all duration-150" 
                   wire:navigate>
                    <svg class="mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('invoices.*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    Invoices
                </a>
            </nav>
        </div>

        {{-- MANAGEMENT SECTION --}}
        <div>
            <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2.5">Management</p>
            <nav class="space-y-1">
                {{-- Account Settings --}}
                <a href="{{ route('profile') }}" 
                   class="{{ request()->routeIs('profile') ? 'bg-[#ED1C24] text-white font-semibold shadow-md shadow-red-900/20' : 'text-slate-400 hover:bg-[#2D2D2D] hover:text-white' }} group flex items-center px-3 py-2.5 text-xs rounded-lg transition-all duration-150" 
                   wire:navigate>
                    <svg class="mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('profile') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Settings
                </a>
            </nav>
        </div>
    </div>

    {{-- Bottom User Profile Card --}}
    <div class="p-3.5 bg-[#1E1E1E] border-t border-[#2D2D2D] flex items-center justify-between">
        <div class="flex items-center space-x-3 truncate">
            <div class="h-9 w-9 rounded-lg bg-[#ED1C24] flex items-center justify-center text-white font-bold text-xs shadow-sm">
                {{ substr(auth()->user()->name ?? 'S', 0, 1) }}
            </div>
            <div class="truncate">
                <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-[10px] text-slate-400 truncate">Administrator</p>
            </div>
        </div>
    </div>
</aside>
