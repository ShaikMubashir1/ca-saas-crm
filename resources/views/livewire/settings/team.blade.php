<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Team & Staff Management</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Manage practice partners, managers, staff members, designations, and system roles.</p>
        </div>
        <button wire:click="openAddModal" class="px-4 py-2 rounded-xl bg-[#ED1C24] text-xs font-bold text-white hover:bg-[#C9141B]">
            + Add Staff Member
        </button>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-3 bg-red-50 text-[#ED1C24] border border-red-200 text-xs rounded-xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search staff by name or email..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
    </div>

    {{-- Staff Table --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
            <thead>
                <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                    <th class="py-3 px-4">Staff Name</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Designation</th>
                    <th class="py-3 px-4">System Role</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @foreach($members as $m)
                    <tr class="hover:bg-[#F7F7F8] transition-colors">
                        <td class="py-3 px-4">
                            <div class="font-bold text-[#252525]">{{ $m->name }}</div>
                            <div class="text-[10px] text-[#737373]">Joined {{ $m->created_at->format('M Y') }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div>{{ $m->email }}</div>
                            <div class="text-[10px] font-mono text-[#737373]">{{ $m->phone ?? '—' }}</div>
                        </td>
                        <td class="py-3 px-4 font-semibold text-[#252525]">
                            {{ $m->designation ?? 'Article / Staff' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-800">
                                {{ $m->roles->first()?->name ?? 'Staff' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold {{ $m->status === 'active' ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-[#ED1C24]' }}">
                                {{ ucfirst($m->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <button wire:click="toggleStatus({{ $m->id }})" class="text-xs font-bold text-[#ED1C24] hover:underline">
                                {{ $m->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $members->links() }}
        </div>
    </div>

    {{-- ADD STAFF MODAL --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-3 mb-4">
                            <h3 class="text-base font-bold text-[#252525]">Add Practice Staff Member</h3>
                            <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        <form wire:submit="createStaff" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Full Name <span class="text-[#ED1C24]">*</span></label>
                                <input wire:model="name" type="text" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Email Address <span class="text-[#ED1C24]">*</span></label>
                                <input wire:model="email" type="email" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Phone Number</label>
                                <input wire:model="phone" type="text" placeholder="919876543210" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Designation</label>
                                <input wire:model="designation" type="text" placeholder="e.g. Senior Audit Assistant" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">System Role</label>
                                <select wire:model="roleName" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                                    <option value="Admin">Admin (Full Access)</option>
                                    <option value="Partner">Partner / CA</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Staff">Staff / Article</option>
                                    <option value="Billing">Billing Clerk</option>
                                </select>
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-xl border border-[#E5E5E5] text-[#252525] font-semibold">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Create Staff
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
