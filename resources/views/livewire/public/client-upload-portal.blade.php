<div class="min-h-screen bg-[#F7F7F8] py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-xl mx-auto space-y-6">

        {{-- Header Branding --}}
        <div class="text-center">
            <h2 class="text-2xl font-bold tracking-tight text-[#252525]">Document Submission Portal</h2>
            <p class="text-xs text-[#737373] mt-1 font-medium">Upload compliance documents for {{ $requestRecord->client->name }}</p>
        </div>

        @if(session()->has('success'))
            <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-2xl font-semibold shadow-xs">
                {{ session('success') }}
            </div>
        @endif

        {{-- Request Info Card --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <div class="border-b border-[#E5E5E5] pb-3 flex justify-between items-center text-xs">
                <div>
                    <span class="font-bold text-[#252525] uppercase text-[10px] tracking-wider block">Service</span>
                    <span class="font-bold text-base text-[#ED1C24]">{{ strtoupper($requestRecord->service->type->value) }}</span>
                </div>
                <div class="text-right">
                    <span class="font-bold text-[#737373] uppercase text-[10px] tracking-wider block">Financial Year</span>
                    <span class="font-bold text-xs text-[#252525]">{{ $requestRecord->financialYear ? $requestRecord->financialYear->year_label : 'FY' }}</span>
                </div>
            </div>

            {{-- Pending Items Checklist --}}
            <div>
                <h4 class="text-xs font-bold text-[#252525] mb-2 uppercase tracking-wider text-[10px]">Requested Items</h4>
                <div class="space-y-2">
                    @foreach($requestRecord->items as $reqItem)
                        <div class="flex items-center justify-between p-3 rounded-xl border {{ $reqItem->received_at ? 'bg-emerald-50/50 border-emerald-200 text-emerald-800' : 'bg-[#F7F7F8] border-[#E5E5E5] text-[#252525]' }} text-xs">
                            <span class="font-semibold">{{ $reqItem->item_name }}</span>
                            @if($reqItem->received_at)
                                <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                    ✓ Received {{ $reqItem->received_at->format('d M') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                                    Pending
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Upload Form --}}
            @if($requestRecord->status->value !== 'completed')
                <div class="pt-4 border-t border-[#E5E5E5]">
                    <h4 class="text-xs font-bold text-[#252525] mb-3">Upload a Pending Document</h4>
                    <form wire:submit="uploadDocument" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-[#252525] mb-1">Select Document Type <span class="text-[#ED1C24]">*</span></label>
                            <select wire:model="selectedItemId" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2.5 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                <option value="">Select item you are uploading...</option>
                                @foreach($requestRecord->items->whereNull('received_at') as $reqItem)
                                    <option value="{{ $reqItem->checklist_item_id }}">{{ $reqItem->item_name }}</option>
                                @endforeach
                            </select>
                            @error('selectedItemId') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-[#252525] mb-1">Attach File <span class="text-[#ED1C24]">*</span></label>
                            <input wire:model="uploadFile" type="file" required class="block w-full text-xs text-[#252525] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#ED1C24] hover:file:bg-red-100">
                            @error('uploadFile') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full py-3 rounded-xl bg-[#ED1C24] text-white font-bold text-xs hover:bg-[#C9141B] transition-all shadow-sm">
                            Submit File to CA Firm
                        </button>
                    </form>
                </div>
            @else
                <div class="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-center font-bold text-xs">
                    🎉 All requested documents for this compliance return have been received!
                </div>
            @endif
        </div>
    </div>
</div>
