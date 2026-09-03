<x-app-layout>
    {{-- Branded Page Header --}}
    <div class="mb-8 md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold tracking-tight text-[#18181B] sm:text-3xl">Executive Practice Dashboard</h1>
            <p class="mt-1 text-sm text-[#71717A]">
                Welcome back, <span class="font-bold text-[#18181B]">{{ auth()->user()->name }}</span>. Here is your firm's operational snapshot.
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('clients.create') }}" class="inline-flex items-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-all duration-150" wire:navigate>
                + Add Client
            </a>
            <a href="{{ route('whatsapp.inbox') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition-all duration-150" wire:navigate>
                WhatsApp Inbox
            </a>
        </div>
    </div>

    {{-- Comprehensive KPI Grid (6 Cards) --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
        {{-- Total Clients --}}
        <div class="bg-white rounded-xl p-4 border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Clients</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-[#18181B]">{{ $totalClients }}</span>
                <span class="text-[10px] font-semibold text-emerald-600">{{ $activeClients }} Active</span>
            </div>
        </div>

        {{-- Tasks --}}
        <div class="bg-white rounded-xl p-4 border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Pending Tasks</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-amber-600">{{ $pendingTasks }}</span>
                <span class="text-[10px] font-semibold text-slate-500">{{ $myTasks }} Mine</span>
            </div>
        </div>

        {{-- Compliance Overdue --}}
        <div class="bg-white rounded-xl p-4 border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-[#ED1C24] uppercase tracking-wider">Overdue Compliance</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-[#ED1C24]">{{ $compMetrics['overdue'] }}</span>
                <span class="text-[10px] font-semibold text-blue-600">{{ $compMetrics['due_7_days'] }} Due 7d</span>
            </div>
        </div>

        {{-- Documents Vault --}}
        <div class="bg-white rounded-xl p-4 border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Vault Files</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-[#18181B]">{{ $totalDocuments }}</span>
                <span class="text-[10px] font-semibold text-amber-600">{{ $pendingDocRequests }} Pending</span>
            </div>
        </div>

        {{-- Outstanding Billing --}}
        <div class="bg-white rounded-xl p-4 border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Outstanding Bal</span>
            <div class="mt-2">
                <span class="text-lg font-extrabold text-[#18181B] font-mono">₹{{ number_format($billingMetrics['outstanding'], 0) }}</span>
            </div>
        </div>

        {{-- WhatsApp Open Chats --}}
        <div class="bg-white rounded-xl p-4 border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Open WA Chats</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-emerald-700">{{ $openConversations }}</span>
                <a href="{{ route('whatsapp.inbox') }}" class="text-[10px] font-bold text-emerald-800 hover:underline">Inbox →</a>
            </div>
        </div>
    </div>

    {{-- Main Two-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 mb-8">
        {{-- Upcoming Compliance Deadlines (3 cols) --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col justify-between">
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#18181B]">Upcoming Statutory Compliance Deadlines</h2>
                <a href="{{ route('compliance.dashboard') }}" class="text-xs font-bold text-[#ED1C24] hover:text-[#C9141B] transition-colors" wire:navigate>
                    Calendar →
                </a>
            </div>

            <div class="p-6">
                @if($upcomingCompliance->isEmpty())
                    <div class="text-center py-8 text-xs text-[#6B7280]">
                        No upcoming compliance filings. All caught up!
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#E5E7EB] text-xs">
                            <thead>
                                <tr class="text-left text-[#6B7280] font-bold uppercase tracking-wider bg-[#F6F7F9]">
                                    <th class="py-2.5 px-3">Client</th>
                                    <th class="py-2.5 px-3">Compliance</th>
                                    <th class="py-2.5 px-3">Period</th>
                                    <th class="py-2.5 px-3">Due Date</th>
                                    <th class="py-2.5 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E5E7EB]">
                                @foreach($upcomingCompliance as $inst)
                                    <tr class="hover:bg-[#F6F7F9] transition-colors">
                                        <td class="py-2.5 px-3 font-bold text-[#18181B]">
                                            <a href="{{ route('clients.show', $inst->client_id) }}" class="hover:text-[#ED1C24]" wire:navigate>{{ $inst->client->name }}</a>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono font-bold">{{ $inst->template->code }}</td>
                                        <td class="py-2.5 px-3 font-mono">{{ $inst->period }}</td>
                                        <td class="py-2.5 px-3 font-bold {{ $inst->due_date && $inst->due_date->isPast() ? 'text-[#ED1C24]' : '' }}">
                                            {{ $inst->due_date ? $inst->due_date->format('d M Y') : '—' }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="inline-flex items-center rounded px-2 py-0.5 text-[9px] font-bold {{ $inst->status->badgeClass() }}">
                                                {{ $inst->status->label() }}
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

        {{-- Recent Practice Activity Log (2 cols) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-[#E5E7EB] shadow-xs p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#18181B] border-b border-[#E5E7EB] pb-2 mb-3">Recent Practice Activity</h3>
                @if($activities->isEmpty())
                    <p class="text-xs text-[#6B7280]">No recent timeline events logged.</p>
                @else
                    <ul class="space-y-3 text-xs">
                        @foreach($activities as $act)
                            <li class="p-2.5 bg-[#F6F7F9] rounded-xl border border-[#E5E7EB]">
                                <div class="flex justify-between items-start">
                                    <span class="font-bold text-[#18181B]">{{ $act->event_type }}</span>
                                    <span class="text-[10px] text-[#6B7280]">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[11px] text-[#71717A] mt-0.5">{{ $act->description }}</p>
                                @if($act->client)
                                    <span class="text-[10px] font-semibold text-[#ED1C24] block mt-1">Client: {{ $act->client->name }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-[#E5E7EB] text-[10px] text-[#6B7280]">
                Standard Touch CA SaaS CRM v2.0 Multi-Tenant
            </div>
        </div>
    </div>
</x-app-layout>
