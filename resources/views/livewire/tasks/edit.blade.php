<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Edit Task</h1>
            <p class="text-xs text-[#737373] mt-1">Update task details, status, or assignee.</p>
        </div>
        <a href="{{ route('tasks.show', $task->id) }}" class="text-xs font-bold text-[#737373] hover:text-[#252525] transition-colors" wire:navigate>
            ← Cancel
        </a>
    </div>

    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-6">
        <form wire:submit="save" class="space-y-5">
            {{-- Client --}}
            <div>
                <label for="client_id" class="block text-xs font-bold text-[#252525]">Client <span class="text-[#ED1C24]">*</span></label>
                <select id="client_id" wire:model="client_id" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    <option value="">Select a Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                @error('client_id') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-bold text-[#252525]">Task Title <span class="text-[#ED1C24]">*</span></label>
                <input type="text" id="title" wire:model="title" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                @error('title') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Service / Compliance Type, Priority & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="service_type" class="block text-xs font-bold text-[#252525]">Service Type <span class="text-[#ED1C24]">*</span></label>
                    <select id="service_type" wire:model="service_type" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('service_type') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="block text-xs font-bold text-[#252525]">Priority <span class="text-[#ED1C24]">*</span></label>
                    <select id="priority" wire:model="priority" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        @foreach($priorities as $priorityOption)
                            <option value="{{ $priorityOption }}">{{ ucfirst($priorityOption) }}</option>
                        @endforeach
                    </select>
                    @error('priority') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-[#252525]">Status <span class="text-[#ED1C24]">*</span></label>
                    <select id="status" wire:model="status" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}">{{ $statusOption === 'in_progress' ? 'In Progress' : ucfirst($statusOption) }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Due Date & Assigned To --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="due_date" class="block text-xs font-bold text-[#252525]">Due Date</label>
                    <input type="date" id="due_date" wire:model="due_date" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('due_date') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="assigned_to" class="block text-xs font-bold text-[#252525]">Assigned Staff Member</label>
                    <select id="assigned_to" wire:model="assigned_to" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-bold text-[#252525]">Description / Notes</label>
                <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all"></textarea>
                @error('description') <p class="mt-1 text-[11px] text-[#ED1C24] font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[#E5E5E5]">
                <a href="{{ route('tasks.show', $task->id) }}" class="px-4 py-2 text-xs font-semibold text-[#252525] bg-white border border-[#E5E5E5] rounded-lg hover:bg-[#F7F7F8] transition-colors" wire:navigate>Cancel</a>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#ED1C24] rounded-lg hover:bg-[#C9141B] shadow-sm transition-colors">Update Task</button>
            </div>
        </form>
    </div>
</div>
