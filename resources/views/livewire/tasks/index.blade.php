<div>
    {{-- Page Header --}}
    <div class="mb-6 md:flex md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Tasks & Compliance</h1>
            <p class="text-xs text-[#737373] mt-1">Track and manage compliance tasks across clients.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-all duration-150" wire:navigate>
                + Create Task
            </a>
        </div>
    </div>

    {{-- Branded KPI Cards Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Total Tasks</span>
            <p class="mt-1 text-2xl font-extrabold text-[#252525]">{{ $totalTasks }}</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">All assigned tasks</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider block">Pending</span>
            <p class="mt-1 text-2xl font-extrabold text-orange-600">{{ $pendingTasks }}</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Awaiting action</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Due Today</span>
            <p class="mt-1 text-2xl font-extrabold text-blue-600">{{ $dueToday }}</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Requires attention</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-[#ED1C24] uppercase tracking-wider block">Overdue</span>
            <p class="mt-1 text-2xl font-extrabold text-[#ED1C24]">{{ $overdueTasks }}</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Past due date</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Completed (Month)</span>
            <p class="mt-1 text-2xl font-extrabold text-emerald-600">{{ $completedThisMonth }}</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Fulfilled this month</span>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-lg font-medium">{{ session('success') }}</div>
    @endif

    {{-- Compact Filter Toolbar --}}
    <div class="bg-white p-3.5 rounded-xl border border-[#E5E5E5] shadow-xs mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks or clients..." class="block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] placeholder-[#737373] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
            </div>
            <select wire:model.live="status" class="block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="overdue">Overdue</option>
            </select>
            <select wire:model.live="priority" class="block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
            <select wire:model.live="serviceType" class="block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                <option value="">All Types</option>
                <option value="GST Return">GST Return</option>
                <option value="TDS Return">TDS Return</option>
                <option value="Income Tax Return">Income Tax Return</option>
                <option value="ROC Filing">ROC Filing</option>
                <option value="Audit">Audit</option>
                <option value="DSC Renewal">DSC Renewal</option>
                <option value="PF / ESI">PF / ESI</option>
                <option value="Professional Tax">Professional Tax</option>
                <option value="Other">Other</option>
            </select>
        </div>
    </div>

    {{-- Task Table / Polished Empty State --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        @if($tasks->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="mx-auto h-12 w-12 rounded-full bg-[#F7F7F8] flex items-center justify-center text-[#737373] mb-3">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#252525]">No tasks found</h3>
                <p class="mt-1 text-xs text-[#737373] max-w-sm mx-auto">
                    Create your first task to start managing compliance activities efficiently.
                </p>
                <div class="mt-4">
                    <a href="{{ route('tasks.create') }}" class="inline-flex items-center rounded-lg bg-[#ED1C24] px-3.5 py-2 text-xs font-semibold text-white hover:bg-[#C9141B] transition-colors" wire:navigate>
                        + Create Task
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-3 px-4">Task</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Priority</th>
                            <th class="py-3 px-4">Due Date</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3.5 px-4 font-bold text-[#252525]">{{ $task->title }}</td>
                                <td class="py-3.5 px-4 text-[#737373] font-medium">{{ $task->client?->name ?? '—' }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-[#252525]">
                                        {{ $task->service_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @php $p = $task->priority; @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-bold
                                        {{ $p === 'low' ? 'bg-slate-100 text-[#737373]' : '' }}
                                        {{ $p === 'medium' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $p === 'high' ? 'bg-orange-50 text-orange-700' : '' }}
                                        {{ $p === 'urgent' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                    ">
                                        {{ ucfirst($p) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-[#737373] font-medium">
                                    {{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php $ds = $task->display_status; @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-bold
                                        {{ $ds === 'pending' ? 'bg-orange-50 text-orange-700' : '' }}
                                        {{ $ds === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $ds === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                        {{ $ds === 'overdue' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                    ">
                                        {{ $ds === 'in_progress' ? 'In Progress' : ucfirst($ds) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold" wire:navigate>View</a>
                                    @if($task->status !== 'completed')
                                        <span class="text-[#E5E5E5]">|</span>
                                        <button wire:click="markComplete({{ $task->id }})" class="text-emerald-600 hover:text-emerald-800 font-bold">Complete</button>
                                    @endif
                                    <span class="text-[#E5E5E5]">|</span>
                                    <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Delete this task?" class="text-slate-400 hover:text-[#ED1C24] font-medium">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-[#E5E5E5]">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
