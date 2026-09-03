<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Compliance Calendar & Filing Engine</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Track statutory filings, GST returns, Income Tax, TDS, and ROC compliance workflows.</p>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Metrics Cards Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs text-center">
            <span class="block text-[10px] font-bold text-[#737373] uppercase">Total Compliance</span>
            <span class="text-lg font-bold text-[#252525]">{{ $metrics['total'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs text-center">
            <span class="block text-[10px] font-bold text-slate-500 uppercase">Upcoming</span>
            <span class="text-lg font-bold text-slate-700">{{ $metrics['upcoming'] }}</span>
        </div>
        <div class="bg-amber-50/60 p-4 rounded-xl border border-amber-200 shadow-xs text-center">
            <span class="block text-[10px] font-bold text-amber-800 uppercase">Docs Pending</span>
            <span class="text-lg font-bold text-amber-900">{{ $metrics['docs_pending'] }}</span>
        </div>
        <div class="bg-blue-50/60 p-4 rounded-xl border border-blue-200 shadow-xs text-center">
            <span class="block text-[10px] font-bold text-blue-800 uppercase">Due In 7 Days</span>
            <span class="text-lg font-bold text-blue-900">{{ $metrics['due_soon'] }}</span>
        </div>
        <div class="bg-red-50/60 p-4 rounded-xl border border-red-200 shadow-xs text-center">
            <span class="block text-[10px] font-bold text-[#ED1C24] uppercase">Overdue</span>
            <span class="text-lg font-bold text-[#ED1C24]">{{ $metrics['overdue'] }}</span>
        </div>
        <div class="bg-emerald-50/60 p-4 rounded-xl border border-emerald-200 shadow-xs text-center">
            <span class="block text-[10px] font-bold text-emerald-800 uppercase">Filed & Ack</span>
            <span class="text-lg font-bold text-emerald-900">{{ $metrics['filed'] }}</span>
        </div>
    </div>

    {{-- Search & Filters Bar --}}
    <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs grid grid-cols-1 md:grid-cols-5 gap-3 text-xs">
        <div class="md:col-span-2">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search client name, compliance code (GST, ITR, TDS)..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
        </div>
        <div>
            <select wire:model.live="selectedStatus" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                <option value="">All Statuses</option>
                @foreach(\App\Enums\ComplianceStatus::cases() as $st)
                    <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="selectedClient" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="selectedServiceType" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                <option value="">All Service Types</option>
                <option value="gst">GST</option>
                <option value="itr">ITR / Income Tax</option>
                <option value="tds">TDS</option>
                <option value="roc">ROC / MCA</option>
                <option value="audit">Tax Audit</option>
            </select>
        </div>
    </div>

    {{-- Compliance Records Table --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        @if($instances->isEmpty())
            <div class="text-center py-12 text-[#737373] space-y-3">
                <p class="text-xs font-semibold text-[#252525]">No compliance instances found for current filters.</p>
                <p class="text-[11px]">Compliance instances are auto-generated based on client services & financial years.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Compliance & Code</th>
                            <th class="py-3 px-4">Period</th>
                            <th class="py-3 px-4">Due Date</th>
                            <th class="py-3 px-4">Assigned / Reviewer</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($instances as $inst)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4">
                                    <a href="{{ route('clients.show', $inst->client_id) }}" class="font-bold text-[#252525] hover:text-[#ED1C24]">
                                        {{ $inst->client->name }}
                                    </a>
                                    <div class="text-[10px] text-[#737373]">{{ $inst->client->client_type->label() }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#252525]">{{ $inst->template->name }}</div>
                                    <span class="inline-flex items-center rounded px-1.5 py-0.2 text-[9px] font-mono font-bold bg-slate-100 text-slate-700">
                                        {{ $inst->template->code }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-[#252525]">
                                    {{ $inst->period }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold {{ $inst->due_date && $inst->due_date->isPast() && !in_array($inst->status->value, ['filed', 'acknowledged', 'cancelled']) ? 'text-[#ED1C24]' : 'text-[#252525]' }}">
                                        {{ $inst->due_date ? $inst->due_date->format('d M Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-[#737373]">
                                    <div>Staff: {{ $inst->assignee ? $inst->assignee->name : 'Unassigned' }}</div>
                                    <div class="text-[10px]">Review: {{ $inst->reviewer ? $inst->reviewer->name : 'None' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold {{ $inst->status->badgeClass() }}">
                                        {{ $inst->status->label() }}
                                    </span>
                                    @if($inst->acknowledgement_number)
                                        <div class="text-[9px] font-mono text-emerald-800 mt-0.5">Ack: {{ $inst->acknowledgement_number }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button wire:click="openStatusModal({{ $inst->id }})" class="text-[#ED1C24] font-bold hover:underline">
                                        Update Workflow
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[#E5E5E5]">
                {{ $instances->links() }}
            </div>
        @endif
    </div>

    {{-- WORKFLOW STATUS MODAL --}}
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showStatusModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-3 mb-4">
                            <h3 class="text-base font-bold text-[#252525]">Update Compliance Workflow State</h3>
                            <button wire:click="$set('showStatusModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        <form wire:submit="updateStatus" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Target Compliance Status <span class="text-[#ED1C24]">*</span></label>
                                <select wire:model="newStatus" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                                    @foreach(\App\Enums\ComplianceStatus::cases() as $st)
                                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($newStatus === 'filed' || $newStatus === 'acknowledged')
                                <div>
                                    <label class="block font-bold text-[#252525] mb-1">Filing Date</label>
                                    <input wire:model="filingDate" type="date" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                                </div>
                                <div>
                                    <label class="block font-bold text-[#252525] mb-1">ARN / Acknowledgement Number</label>
                                    <input wire:model="acknowledgementNumber" type="text" placeholder="e.g. AA270826000123" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                                </div>
                            @endif

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Workflow Notes / Activity Remark</label>
                                <textarea wire:model="transitionNotes" rows="2" placeholder="Detail progress or review comments..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] p-2 px-3 text-xs text-[#252525]"></textarea>
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showStatusModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
