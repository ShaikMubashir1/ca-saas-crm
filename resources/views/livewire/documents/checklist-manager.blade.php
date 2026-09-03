<div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs space-y-6">
    {{-- Header & Metrics Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#E5E5E5] pb-4">
        <div>
            <h3 class="text-sm font-bold text-[#252525] flex items-center gap-2">
                <span>Document Checklist</span>
                <span class="inline-flex items-center rounded-md bg-red-50 text-[#ED1C24] px-2 py-0.5 text-[10px] font-bold">
                    {{ strtoupper($service->type->value) }}
                </span>
            </h3>
            <p class="text-[11px] text-[#737373] mt-0.5">Required and optional files for {{ $service->financialYear ? $service->financialYear->year_label : 'FY' }} compliance.</p>
        </div>

        <div class="flex items-center gap-3">
            @if($metrics)
                <div class="flex items-center gap-4 text-xs bg-[#F7F7F8] p-2.5 rounded-xl border border-[#E5E5E5]">
                    <div class="text-center">
                        <span class="text-[10px] text-[#737373] font-bold block uppercase">Required</span>
                        <span class="font-bold text-[#252525]">{{ $metrics['total_required'] }}</span>
                    </div>
                    <div class="h-6 w-px bg-[#E5E5E5]"></div>
                    <div class="text-center">
                        <span class="text-[10px] text-emerald-700 font-bold block uppercase">Verified</span>
                        <span class="font-bold text-emerald-800">{{ $metrics['verified_required'] }}</span>
                    </div>
                    <div class="h-6 w-px bg-[#E5E5E5]"></div>
                    <div class="text-center">
                        <span class="text-[10px] text-amber-700 font-bold block uppercase">Pending</span>
                        <span class="font-bold text-amber-800">{{ $metrics['pending'] }}</span>
                    </div>
                    <div class="h-6 w-px bg-[#E5E5E5]"></div>
                    <div class="text-center">
                        <span class="text-[10px] text-red-700 font-bold block uppercase">Rejected</span>
                        <span class="font-bold text-[#ED1C24]">{{ $metrics['rejected'] }}</span>
                    </div>
                </div>
            @endif

            <button wire:click="openRequestModal" class="inline-flex items-center justify-center rounded-lg bg-[#252525] px-3.5 py-2 text-xs font-bold text-white hover:bg-black transition-colors shadow-xs">
                ✉ Request Documents
            </button>
        </div>
    </div>

    {{-- Progress Bar --}}
    @if($metrics)
        <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-bold">
                <span class="text-[#252525]">Checklist Completion</span>
                <span class="{{ $metrics['percentage'] === 100 ? 'text-emerald-700' : 'text-[#ED1C24]' }}">{{ $metrics['percentage'] }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-[#ED1C24] h-2.5 rounded-full transition-all duration-300" style="width: {{ $metrics['percentage'] }}%"></div>
            </div>
        </div>
    @endif

    {{-- Items Table --}}
    @if(!$checklist || $checklist->items->isEmpty())
        <div class="text-center py-6 text-[#737373]">
            <p class="text-xs font-medium">No checklist items generated for this service.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                <thead>
                    <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                        <th class="py-2.5 px-3">Item / Requirement</th>
                        <th class="py-2.5 px-3">Category</th>
                        <th class="py-2.5 px-3">Requirement</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3">Current File</th>
                        <th class="py-2.5 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($checklist->items as $item)
                        <tr class="hover:bg-[#F7F7F8] transition-colors">
                            <td class="py-3 px-3">
                                <div class="font-bold text-[#252525]">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="text-[10px] text-[#737373]">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-[#737373] font-medium">
                                {{ $item->document_type ?? 'General' }}
                            </td>
                            <td class="py-3 px-3">
                                @if($item->is_required)
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-bold bg-red-50 text-[#ED1C24]">
                                        Required
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-semibold bg-slate-100 text-[#737373]">
                                        Optional
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @php $st = $item->status->value; @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold
                                    {{ $st === 'verified' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                    {{ $st === 'received' || $st === 'under_review' ? 'bg-blue-50 text-blue-800' : '' }}
                                    {{ $st === 'pending' ? 'bg-amber-50 text-amber-800' : '' }}
                                    {{ $st === 'rejected' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                ">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @if($item->currentDocument)
                                    <div class="font-semibold text-[#252525] flex items-center gap-1">
                                        <a href="{{ route('documents.download', $item->currentDocument->id) }}" class="text-[#ED1C24] hover:underline font-mono text-[11px]" target="_blank">
                                            {{ $item->currentDocument->name }}
                                        </a>
                                    </div>
                                    <div class="text-[10px] text-[#737373]">
                                        Uploaded {{ $item->currentDocument->created_at->format('d M, H:i') }}
                                    </div>
                                @else
                                    <span class="text-[#737373] text-[11px]">No file attached</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right space-x-2">
                                @if($item->currentDocument)
                                    @if($item->status->value !== 'verified')
                                        <button wire:click="verifyItem({{ $item->id }})" class="text-emerald-700 hover:text-emerald-900 font-bold">
                                            Verify
                                        </button>
                                        <span class="text-[#E5E5E5]">|</span>
                                    @endif
                                    @if($item->status->value !== 'rejected')
                                        <button wire:click="openRejectModal({{ $item->id }})" class="text-[#ED1C24] hover:text-[#C9141B] font-bold">
                                            Reject
                                        </button>
                                        <span class="text-[#E5E5E5]">|</span>
                                    @endif
                                @endif

                                <button wire:click="openUploadModal({{ $item->id }})" class="text-blue-700 hover:text-blue-900 font-bold">
                                    {{ $item->currentDocument ? 'Replace' : 'Upload' }}
                                </button>

                                <span class="text-[#E5E5E5]">|</span>
                                <button wire:click="openHistoryModal({{ $item->id }})" class="text-slate-500 hover:text-[#252525] font-semibold">
                                    History ({{ $item->documents->count() }})
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- REJECT REASON MODAL --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showRejectModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-base font-bold text-[#252525] mb-2">Reject Document</h3>
                        <p class="text-xs text-[#737373] mb-4">Please provide a clear reason for rejecting this document so staff/client can re-upload.</p>

                        <form wire:submit="confirmReject" class="space-y-4 text-xs">
                            <div>
                                <label for="rejectionReason" class="block font-bold text-[#252525]">Rejection Reason <span class="text-[#ED1C24]">*</span></label>
                                <textarea wire:model="rejectionReason" id="rejectionReason" rows="3" required class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]" placeholder="e.g. Blurred text, missing page 2, incorrect FY year..."></textarea>
                                @error('rejectionReason') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showRejectModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Reject Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DIRECT UPLOAD / REPLACE MODAL --}}
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showUploadModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-base font-bold text-[#252525] mb-2">Upload Document File</h3>
                        <p class="text-xs text-[#737373] mb-4">Attach a replacement file for this checklist requirement.</p>

                        <form wire:submit="uploadReplacement" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-[#252525]">Select File <span class="text-[#ED1C24]">*</span></label>
                                <input wire:model="uploadFile" type="file" required class="mt-1 block w-full text-xs text-[#252525] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#ED1C24] hover:file:bg-red-100">
                                @error('uploadFile') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showUploadModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Upload File
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DOCUMENT HISTORY MODAL --}}
    @if($showHistoryModal && $historyItem)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showHistoryModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-3 mb-4">
                            <h3 class="text-base font-bold text-[#252525]">Document Audit History — {{ $historyItem->name }}</h3>
                            <button wire:click="$set('showHistoryModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        @if($historyItem->documents->isEmpty())
                            <p class="text-xs text-[#737373]">No file uploads recorded for this item yet.</p>
                        @else
                            <div class="space-y-3 max-h-96 overflow-y-auto text-xs pr-1">
                                @foreach($historyItem->documents as $doc)
                                    <div class="p-3.5 rounded-xl border {{ $doc->is_current ? 'border-[#ED1C24] bg-red-50/30' : 'border-[#E5E5E5] bg-[#F7F7F8]' }}">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-[#252525] font-mono text-[11px]">{{ $doc->name }}</span>
                                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-bold
                                                {{ $doc->status->value === 'verified' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $doc->status->value === 'rejected' ? 'bg-red-100 text-[#ED1C24]' : '' }}
                                                {{ $doc->status->value === 'received' ? 'bg-blue-100 text-blue-800' : '' }}
                                            ">
                                                {{ $doc->status->label() }} {{ $doc->is_current ? '(Current)' : '(Archived)' }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-[#737373] space-y-0.5">
                                            <div>Uploaded by: <span class="font-semibold text-[#252525]">{{ $doc->uploader ? $doc->uploader->name : 'System' }}</span> on {{ $doc->created_at->format('d M Y, h:i A') }}</div>
                                            @if($doc->verified_by)
                                                <div>Verified by: <span class="font-semibold text-emerald-800">{{ $doc->verifier ? $doc->verifier->name : 'Staff' }}</span> on {{ $doc->verified_at ? $doc->verified_at->format('d M Y') : '' }}</div>
                                            @endif
                                            @if($doc->rejection_reason)
                                                <div class="text-[#ED1C24] font-semibold mt-1">Rejection Reason: {{ $doc->rejection_reason }}</div>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-right">
                                            <a href="{{ route('documents.download', $doc->id) }}" class="text-[#ED1C24] font-bold text-[11px] hover:underline">Download File →</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-t border-[#E5E5E5] flex justify-end">
                            <button wire:click="$set('showHistoryModal', false)" class="px-4 py-2 rounded-lg bg-slate-100 text-[#252525] font-semibold hover:bg-slate-200">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DOCUMENT REQUEST MODAL --}}
    @if($showRequestModal && $checklist)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showRequestModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-3 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-[#252525]">Request Pending Documents</h3>
                                <p class="text-xs text-[#737373] mt-0.5">{{ $service->client->name }} • {{ strtoupper($service->type->value) }} ({{ $service->financialYear ? $service->financialYear->year_label : 'FY' }})</p>
                            </div>
                            <button wire:click="$set('showRequestModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        <div class="space-y-4 text-xs">
                            {{-- Select Items --}}
                            <div>
                                <label class="block font-bold text-[#252525] mb-2">Select Documents to Request</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-[#E5E5E5] rounded-xl p-3 bg-[#F7F7F8]">
                                    @foreach($checklist->items as $chkItem)
                                        <label class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                            <input type="checkbox" wire:model="selectedRequestItems" wire:change="updateRequestPreview" value="{{ $chkItem->id }}" class="rounded border-[#E5E5E5] text-[#ED1C24] focus:ring-[#ED1C24]">
                                            <span class="font-semibold text-[#252525]">{{ $chkItem->name }}</span>
                                            @if($chkItem->is_required)
                                                <span class="text-[10px] text-[#ED1C24] font-bold">(Required)</span>
                                            @endif
                                            <span class="ml-auto text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-slate-200 text-[#737373]">{{ $chkItem->status->label() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Message Preview --}}
                            <div>
                                <label for="requestMessage" class="block font-bold text-[#252525] mb-1">Generated Request Message Preview</label>
                                <textarea wire:model="requestMessage" id="requestMessage" rows="5" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] p-3 text-xs text-[#252525] font-mono focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]"></textarea>
                            </div>

                            {{-- Modal Actions --}}
                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showRequestModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="button" wire:click="createDocumentRequest('draft')" class="px-4 py-2 rounded-lg bg-slate-100 text-[#252525] font-bold hover:bg-slate-200">
                                    Save Draft
                                </button>
                                <button type="button" wire:click="createDocumentRequest('sent')" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Create & Send Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
