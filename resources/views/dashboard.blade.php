<x-app-layout>
    {{-- Branded Page Header --}}
    <div class="mb-8 md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold tracking-tight text-[#18181B] sm:text-3xl">Dashboard</h1>
            <p class="mt-1 text-sm text-[#71717A]">
                Welcome back, <span class="font-bold text-[#18181B]">{{ auth()->user()->name }}</span>. Here's what's happening with your workspace.
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('clients.create') }}" class="inline-flex items-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ED1C24] transition-all duration-150" wire:navigate>
                + Add Client
            </a>
        </div>
    </div>

    {{-- Compact 4-Column KPI Grid (Heights ~130-140px) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Card 1: Total Clients --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-xs hover:border-slate-300 hover:shadow-sm transition-all duration-150 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#6B7280] uppercase tracking-wider">Total Clients</span>
                <div class="h-8 w-8 rounded-lg bg-[#FDF2F2] flex items-center justify-center text-[#ED1C24]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-3xl font-extrabold text-[#18181B] tracking-tight leading-none">{{ $totalClients }}</div>
                <p class="mt-1 text-[11px] text-[#6B7280]">Registered client accounts</p>
            </div>
        </div>

        {{-- Card 2: Pending Tasks --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-xs hover:border-slate-300 hover:shadow-sm transition-all duration-150 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#6B7280] uppercase tracking-wider">Pending Tasks</span>
                <div class="h-8 w-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-3xl font-extrabold text-[#18181B] tracking-tight leading-none">{{ $pendingTasks }}</div>
                <p class="mt-1 text-[11px] text-amber-600 font-medium">Pending action</p>
            </div>
        </div>

        {{-- Card 3: Completed This Month --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-xs hover:border-slate-300 hover:shadow-sm transition-all duration-150 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#6B7280] uppercase tracking-wider">Completed (Month)</span>
                <div class="h-8 w-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-3xl font-extrabold text-[#18181B] tracking-tight leading-none">{{ $completedThisMonth }}</div>
                <p class="mt-1 text-[11px] text-emerald-600 font-medium">Compliance fulfilled</p>
            </div>
        </div>

        {{-- Card 4: Vault Documents --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] shadow-xs hover:border-slate-300 hover:shadow-sm transition-all duration-150 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#6B7280] uppercase tracking-wider">Vault Documents</span>
                <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center text-[#252525]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-3xl font-extrabold text-[#18181B] tracking-tight leading-none">{{ $totalDocuments }}</div>
                <p class="mt-1 text-[11px] text-[#6B7280]">Files stored securely</p>
            </div>
        </div>
    </div>

    {{-- Main 60 / 40 Two-Column Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 mb-8">
        {{-- Recent Activity Card (60% / 3 cols out of 5) --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col justify-between">
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#18181B]">Recent Activity</h2>
                <a href="{{ route('tasks.index') }}" class="text-xs font-bold text-[#ED1C24] hover:text-[#C9141B] transition-colors" wire:navigate>
                    View all →
                </a>
            </div>

            <div class="p-6 flex-1">
                @if($activities->isEmpty())
                    <div class="text-center py-10">
                        <div class="mx-auto h-10 w-10 rounded-full bg-[#F6F7F9] flex items-center justify-center text-slate-400 mb-2.5">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#18181B]">No recent activity</h3>
                        <p class="mt-1 text-xs text-[#6B7280] max-w-sm mx-auto">
                            Your latest CRM activity will appear here as you manage clients and compliance tasks.
                        </p>
                    </div>
                @else
                    <ul role="list" class="divide-y divide-[#E5E7EB]">
                        @foreach($activities as $act)
                            <li class="py-3.5 flex items-center justify-between hover:bg-[#F6F7F9] -mx-6 px-6 transition-colors">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="h-8 w-8 rounded-lg flex items-center justify-center shrink-0 
                                        {{ $act['type'] === 'client' ? 'bg-slate-100 text-[#252525]' : '' }}
                                        {{ $act['type'] === 'task' ? 'bg-amber-50 text-amber-600' : '' }}
                                        {{ $act['type'] === 'document' ? 'bg-[#FDF2F2] text-[#ED1C24]' : '' }}
                                    ">
                                        @if($act['type'] === 'client')
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        @elseif($act['type'] === 'task')
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ $act['url'] }}" class="text-xs font-semibold text-[#18181B] hover:text-[#ED1C24] truncate block" wire:navigate>
                                            {{ $act['title'] }}
                                        </a>
                                        @if($act['subtitle'])
                                            <p class="text-[11px] text-[#6B7280] truncate">{{ $act['subtitle'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-[11px] text-[#6B7280] font-medium shrink-0 ml-4">
                                    {{ $act['created_at']->diffForHumans() }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Quick Actions Panel (40% / 2 cols out of 5) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-[#E5E7EB] shadow-xs p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#18181B] mb-0.5">Quick Actions</h3>
                <p class="text-xs text-[#6B7280] mb-4">Frequently performed operations for rapid access.</p>

                <div class="space-y-2.5">
                    <a href="{{ route('clients.create') }}" class="flex items-center justify-between p-3.5 rounded-xl border border-[#E5E7EB] hover:border-[#ED1C24] hover:bg-[#FDF2F2]/50 transition-all duration-150 group" wire:navigate>
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-lg bg-[#FDF2F2] text-[#ED1C24] flex items-center justify-center font-bold text-sm">
                                +
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#18181B] group-hover:text-[#ED1C24]">Add New Client</p>
                                <p class="text-[11px] text-[#6B7280]">Register entity profile</p>
                            </div>
                        </div>
                        <span class="text-xs text-[#6B7280] group-hover:text-[#ED1C24] transition-colors">→</span>
                    </a>

                    <a href="{{ route('tasks.create') }}" class="flex items-center justify-between p-3.5 rounded-xl border border-[#E5E7EB] hover:border-[#ED1C24] hover:bg-[#FDF2F2]/50 transition-all duration-150 group" wire:navigate>
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                                ✓
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#18181B] group-hover:text-[#ED1C24]">Create Task</p>
                                <p class="text-[11px] text-[#6B7280]">Assign compliance activity</p>
                            </div>
                        </div>
                        <span class="text-xs text-[#6B7280] group-hover:text-[#ED1C24] transition-colors">→</span>
                    </a>

                    <a href="{{ route('clients.index') }}" class="flex items-center justify-between p-3.5 rounded-xl border border-[#E5E7EB] hover:border-[#ED1C24] hover:bg-[#FDF2F2]/50 transition-all duration-150 group" wire:navigate>
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-[#252525] flex items-center justify-center text-sm">
                                📄
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#18181B] group-hover:text-[#ED1C24]">Upload Document</p>
                                <p class="text-[11px] text-[#6B7280]">Store in client vault</p>
                            </div>
                        </div>
                        <span class="text-xs text-[#6B7280] group-hover:text-[#ED1C24] transition-colors">→</span>
                    </a>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#E5E7EB] text-[11px] text-[#6B7280]">
                Standard Touch e-Solutions CRM v2.0
            </div>
        </div>
    </div>

    {{-- Upcoming Tasks Card Section --}}
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#18181B]">Upcoming Tasks</h2>
            <a href="{{ route('tasks.index') }}" class="text-xs font-bold text-[#ED1C24] hover:text-[#C9141B] transition-colors" wire:navigate>
                View all →
            </a>
        </div>

        <div class="p-6">
            @if($upcomingTasks->isEmpty())
                <div class="text-center py-8">
                    <div class="mx-auto h-10 w-10 rounded-full bg-[#F6F7F9] flex items-center justify-center text-emerald-600 mb-2">
                        ✓
                    </div>
                    <h3 class="text-sm font-bold text-[#18181B]">No upcoming tasks</h3>
                    <p class="mt-1 text-xs text-[#6B7280]">You're all caught up on your compliance queue.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E5E7EB] text-xs">
                        <thead>
                            <tr class="text-left text-[#6B7280] font-bold uppercase tracking-wider bg-[#F6F7F9]">
                                <th class="py-2.5 px-4">Task</th>
                                <th class="py-2.5 px-4">Client</th>
                                <th class="py-2.5 px-4">Due Date</th>
                                <th class="py-2.5 px-4">Priority</th>
                                <th class="py-2.5 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E7EB]">
                            @foreach($upcomingTasks as $task)
                                <tr class="hover:bg-[#F6F7F9] transition-colors">
                                    <td class="py-3 px-4 font-bold text-[#18181B]">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="hover:text-[#ED1C24]" wire:navigate>{{ $task->title }}</a>
                                    </td>
                                    <td class="py-3 px-4 text-[#6B7280]">{{ $task->client?->name ?? '—' }}</td>
                                    <td class="py-3 px-4 text-[#6B7280] font-medium">{{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}</td>
                                    <td class="py-3 px-4">
                                        @php $p = $task->priority; @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-bold
                                            {{ $p === 'low' ? 'bg-slate-100 text-[#6B7280]' : '' }}
                                            {{ $p === 'medium' ? 'bg-blue-50 text-blue-700' : '' }}
                                            {{ $p === 'high' ? 'bg-orange-50 text-orange-700' : '' }}
                                            {{ $p === 'urgent' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                        ">
                                            {{ ucfirst($p) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @php $ds = $task->display_status; @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-bold
                                            {{ $ds === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                                            {{ $ds === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                                            {{ $ds === 'overdue' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                        ">
                                            {{ $ds === 'in_progress' ? 'In Progress' : ucfirst($ds) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
