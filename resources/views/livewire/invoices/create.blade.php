<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Create New Invoice</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Generate a professional tax invoice for CA services and client billing.</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-xs font-semibold text-[#252525] hover:bg-[#F7F7F8]">
            ← Back to Invoices
        </a>
    </div>

    @if(session()->has('warning'))
        <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200 text-xs rounded-xl font-semibold">
            {{ session('warning') }}
        </div>
    @endif

    <form wire:submit="save('draft')" class="space-y-6">
        {{-- Invoice Header Info --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Invoice & Client Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Select Client <span class="text-[#ED1C24]">*</span></label>
                    <select wire:model.live="client_id" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2.5 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                        <option value="">Choose a client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->client_type->label() }})</option>
                        @endforeach
                    </select>
                    @error('client_id') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#252525] mb-1">Invoice Date <span class="text-[#ED1C24]">*</span></label>
                    <input wire:model="invoice_date" type="date" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2.5 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                    @error('invoice_date') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#252525] mb-1">Due Date <span class="text-[#ED1C24]">*</span></label>
                    <input wire:model="due_date" type="date" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2.5 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                    @error('due_date') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Line Items Table --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-2">
                <h3 class="text-sm font-bold text-[#252525]">Services & Fee Breakdown</h3>
                <button type="button" wire:click="addItem" class="text-xs font-bold text-[#ED1C24] hover:underline">
                    + Add Item Line
                </button>
            </div>

            <div class="space-y-3">
                @foreach($items as $index => $item)
                    <div class="p-3 bg-[#F7F7F8] rounded-xl border border-[#E5E5E5] grid grid-cols-12 gap-3 items-end text-xs">
                        <div class="col-span-12 md:col-span-3">
                            <label class="block font-semibold text-[#737373] text-[10px] uppercase mb-1">Service (Optional)</label>
                            <select wire:model.live="items.{{ $index }}.service_id" class="w-full rounded-lg border-[#E5E5E5] bg-white py-2 px-2 text-xs text-[#252525]">
                                <option value="">Select Service...</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ strtoupper($svc->type->value) }} ({{ $svc->financialYear ? $svc->financialYear->year_label : 'FY' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label class="block font-semibold text-[#737373] text-[10px] uppercase mb-1">Description *</label>
                            <input wire:model="items.{{ $index }}.description" type="text" placeholder="e.g. Audit & Tax Return Filing Fees" required class="w-full rounded-lg border-[#E5E5E5] bg-white py-2 px-3 text-xs text-[#252525]">
                            @error("items.{$index}.description") <span class="text-[#ED1C24] text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-4 md:col-span-1">
                            <label class="block font-semibold text-[#737373] text-[10px] uppercase mb-1">Qty</label>
                            <input wire:model.live="items.{{ $index }}.quantity" type="number" step="0.01" min="0.01" required class="w-full rounded-lg border-[#E5E5E5] bg-white py-2 px-2 text-xs text-[#252525] text-center">
                        </div>

                        <div class="col-span-4 md:col-span-2">
                            <label class="block font-semibold text-[#737373] text-[10px] uppercase mb-1">Unit Rate (₹)</label>
                            <input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" min="0" required class="w-full rounded-lg border-[#E5E5E5] bg-white py-2 px-2 text-xs text-[#252525]">
                        </div>

                        <div class="col-span-3 md:col-span-1">
                            <label class="block font-semibold text-[#737373] text-[10px] uppercase mb-1">GST %</label>
                            <input wire:model.live="items.{{ $index }}.tax_rate" type="number" step="1" min="0" required class="w-full rounded-lg border-[#E5E5E5] bg-white py-2 px-1 text-xs text-[#252525] text-center">
                        </div>

                        <div class="col-span-1 text-right pb-2">
                            @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-[#ED1C24] font-bold hover:text-red-700">✕</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary Breakdown --}}
            <div class="pt-4 border-t border-[#E5E5E5] flex flex-col md:flex-row justify-between gap-6 text-xs">
                <div class="w-full md:w-1/2 space-y-3">
                    <div>
                        <label class="block font-bold text-[#252525] mb-1">Discount Amount (₹)</label>
                        <input wire:model.live="discount_amount" type="number" step="0.01" min="0" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                    </div>
                    <div>
                        <label class="block font-bold text-[#252525] mb-1">Notes / Remarks</label>
                        <textarea wire:model="notes" rows="2" placeholder="Notes visible to client..." class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] p-2.5 text-xs text-[#252525]"></textarea>
                    </div>
                </div>

                <div class="w-full md:w-5/12 bg-[#F7F7F8] p-4 rounded-xl border border-[#E5E5E5] space-y-2 font-mono">
                    <div class="flex justify-between text-[#737373]">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format($this->calculateSubtotal(), 2) }}</span>
                    </div>
                    <div class="flex justify-between text-[#737373]">
                        <span>GST Tax Amount:</span>
                        <span>₹{{ number_format($this->calculateTax(), 2) }}</span>
                    </div>
                    @if($discount_amount > 0)
                        <div class="flex justify-between text-emerald-700">
                            <span>Discount:</span>
                            <span>- ₹{{ number_format((float)$discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm font-bold text-[#252525] pt-2 border-t border-[#E5E5E5]">
                        <span>Total Invoice Amount:</span>
                        <span class="text-[#ED1C24]">₹{{ number_format($this->calculateTotal(), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex justify-end gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl border border-[#E5E5E5] text-xs font-bold text-[#252525] bg-white hover:bg-[#F7F7F8]">
                Save as Draft
            </button>
            <button type="button" wire:click="save('sent')" class="px-6 py-2.5 rounded-xl bg-[#ED1C24] text-xs font-bold text-white hover:bg-[#C9141B] shadow-sm">
                Save & Issue Invoice
            </button>
        </div>
    </form>
</div>
