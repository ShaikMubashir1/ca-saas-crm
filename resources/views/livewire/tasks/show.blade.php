<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('tasks.index') }}" class="text-xs font-bold text-[#737373] hover:text-[#252525] transition-colors" wire:navigate>
                ← Back to Tasks
            </a>
            <h1 class="text-2xl font-bold text-[#252525] mt-1">{{ $task->title }}</h1>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('tasks.edit', $task->id) }}" class="inline-flex items-center rounded-lg border border-[#E5E5E5] bg-white px-3.5 py-2 text-xs font-semibold text-[#252525] shadow-xs hover:bg-[#F7F7F8]" wire:navigate>
                Edit
            </a>
            @if($task->status === 'completed')
                <button wire:click="reopenTask" class="inline-flex items-center rounded-lg bg-orange-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-orange-700">
                    Reopen Task
                </button>
            @else
                <button wire:click="markComplete" class="inline-flex items-center rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                    Mark Complete
                </button>
            @endif
            <button wire:click="deleteTask" wire:confirm="Delete task?" class="inline-flex items-center rounded-lg bg-[#ED1C24] px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B]">
                Delete
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-lg font-medium">{{ session('success') }}</div>
    @endif

    {{-- Task Details Card --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-6 space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pb-6 border-b border-[#E5E5E5]">
            <div>
                <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Status</span>
                @php $ds = $task->display_status; @endphp
                <span class="mt-1 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold
                    {{ $ds === 'pending' ? 'bg-orange-50 text-orange-700' : '' }}
                    {{ $ds === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                    {{ $ds === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                    {{ $ds === 'overdue' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                ">
                    {{ $ds === 'in_progress' ? 'In Progress' : ucfirst($ds) }}
                </span>
            </div>

            <div>
                <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Priority</span>
                @php $p = $task->priority; @endphp
                <span class="mt-1 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold
                    {{ $p === 'low' ? 'bg-slate-100 text-[#737373]' : '' }}
                    {{ $p === 'medium' ? 'bg-blue-50 text-blue-700' : '' }}
                    {{ $p === 'high' ? 'bg-orange-50 text-orange-700' : '' }}
                    {{ $p === 'urgent' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                ">
                    {{ ucfirst($p) }}
                </span>
            </div>

            <div>
                <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Service Type</span>
                <span class="mt-1 text-xs font-bold text-[#252525] block">{{ $task->service_type }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Due Date</span>
                <span class="mt-1 text-xs font-bold text-[#252525] block">{{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="col-span-2 space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-[#737373] uppercase tracking-wider">Description / Notes</h3>
                    <p class="mt-1.5 text-xs text-[#252525] whitespace-pre-wrap leading-relaxed">{{ $task->description ?: 'No description provided.' }}</p>
                </div>

                @if($task->completed_at)
                    <div class="p-3 bg-emerald-50 rounded-lg text-xs font-medium text-emerald-800 border border-emerald-200">
                        Completed on {{ $task->completed_at->format('d M Y, h:i A') }}
                    </div>
                @endif
            </div>

            <div class="space-y-4 bg-[#F7F7F8] p-4 rounded-xl border border-[#E5E5E5]">
                <div>
                    <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Client</span>
                    @if($task->client)
                        <a href="{{ route('clients.show', $task->client->id) }}" class="text-xs font-bold text-[#ED1C24] hover:text-[#C9141B] mt-0.5 block" wire:navigate>
                            {{ $task->client->name }}
                        </a>
                    @else
                        <span class="text-xs text-[#737373]">—</span>
                    @endif
                </div>

                <div>
                    <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Assigned Staff</span>
                    <span class="text-xs font-bold text-[#252525] mt-0.5 block">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Created By</span>
                    <span class="text-xs text-[#252525] font-medium mt-0.5 block">{{ $task->creator?->name ?? 'System' }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Created At</span>
                    <span class="text-xs text-[#737373] mt-0.5 block">{{ $task->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
