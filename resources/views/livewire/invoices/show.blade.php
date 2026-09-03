<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header & Action Controls --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Invoice {{ $invoice->invoice_number }}</h1>
                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-bold {{ $invoice->status->badgeClass() }}">
                    {{ $invoice->status->label() }}
                </span>
            </div>
            <p class="text-xs text-[#737373] mt-1 font-medium">Issued for {{ $invoice->client->name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 rounded-lg border border-[#E5E5E5] bg-white text-xs font-bold text-[#252525] hover:bg-[#F7F7F8]">
                🖨 Print / PDF
            </button>

            @if($invoice->status !== \App\Enums\InvoiceStatus::CANCELLED && $invoice->balance_due > 0)
                <button wire:click="openPaymentModal" class="px-4 py-2 rounded-lg bg-emerald-600 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm">
                    + Record Payment
                </button>
            @endif

            @if($invoice->status === \App\Enums\InvoiceStatus::DRAFT)
                <button wire:click="sendInvoice" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-xs font-bold text-white hover:bg-[#C9141B] shadow-sm">
                    Send & Issue
                </button>
            @endif

            @if($invoice->status !== \App\Enums\InvoiceStatus::CANCELLED && $invoice->payments->isEmpty())
                <button wire:click="cancelInvoice" wire:confirm="Are you sure you want to cancel this invoice?" class="px-3 py-2 rounded-lg border border-red-200 text-xs font-bold text-[#ED1C24] hover:bg-red-50">
                    Cancel
                </button>
            @endif
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold print:hidden">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('warning'))
        <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200 text-xs rounded-xl font-semibold print:hidden">
            {{ session('warning') }}
        </div>
    @endif

    {{-- Printable Tax Invoice Document Card --}}
    <div class="bg-white p-8 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-6 text-xs text-[#252525] font-sans print:border-none print:shadow-none print:p-0">
        {{-- Firm Header & Invoice Metadata --}}
        <div class="flex justify-between items-start border-b border-[#E5E5E5] pb-6">
            <div>
                <h2 class="text-xl font-bold text-[#ED1C24] uppercase tracking-wider">{{ Auth::user()->tenant ? Auth::user()->tenant->name : 'CHARTERED ACCOUNTANTS' }}</h2>
                <p class="text-xs text-[#737373] font-medium mt-0.5">Chartered Accountant & Financial Advisory Services</p>
                <p class="text-[11px] text-[#737373] mt-2">GSTIN / PAN: Provider Compliance Registered</p>
            </div>
            <div class="text-right space-y-1">
                <h3 class="text-lg font-bold text-[#252525]">TAX INVOICE</h3>
                <div class="font-mono text-xs font-bold text-[#ED1C24]">{{ $invoice->invoice_number }}</div>
                <div class="text-[11px] text-[#737373]">Issue Date: <span class="font-semibold text-[#252525]">{{ $invoice->issue_date->format('d M Y') }}</span></div>
                <div class="text-[11px] text-[#737373]">Due Date: <span class="font-semibold text-[#252525]">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</span></div>
            </div>
        </div>

        {{-- Client Info --}}
        <div class="grid grid-cols-2 gap-6 bg-[#F7F7F8] p-4 rounded-xl border border-[#E5E5E5]">
            <div>
                <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block mb-1">Billed To</span>
                <div class="font-bold text-sm text-[#252525]">{{ $invoice->client->name }}</div>
                <div class="text-[#737373] mt-0.5">{{ $invoice->client->client_type->label() }}</div>
                <div class="text-[#737373] mt-0.5">{{ $invoice->client->phone ?? 'No Phone' }} | {{ $invoice->client->email ?? 'No Email' }}</div>
            </div>
            <div class="text-right space-y-1">
                <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block mb-1">Payment Status</span>
                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-bold {{ $invoice->status->badgeClass() }}">
                    {{ $invoice->status->label() }}
                </span>
                <div class="text-xs font-mono font-bold text-[#252525] mt-1">Outstanding Balance: ₹{{ number_format($invoice->balance_due, 2) }}</div>
            </div>
        </div>

        {{-- Items Table --}}
        <div>
            <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                <thead>
                    <tr class="text-left text-[#737373] font-bold uppercase tracking-wider border-b border-[#E5E5E5]">
                        <th class="py-2.5 px-2">#</th>
                        <th class="py-2.5 px-2">Description</th>
                        <th class="py-2.5 px-2 text-center">Qty</th>
                        <th class="py-2.5 px-2 text-right">Unit Price</th>
                        <th class="py-2.5 px-2 text-center">GST %</th>
                        <th class="py-2.5 px-2 text-right">Tax Amount</th>
                        <th class="py-2.5 px-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($invoice->items as $idx => $item)
                        <tr>
                            <td class="py-3 px-2 text-[#737373]">{{ $idx + 1 }}</td>
                            <td class="py-3 px-2">
                                <div class="font-bold text-[#252525]">{{ $item->description }}</div>
                                @if($item->service)
                                    <div class="text-[10px] text-[#737373]">Linked Service: {{ strtoupper($item->service->type->value) }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-center text-[#737373]">{{ number_format($item->quantity, 2) }}</td>
                            <td class="py-3 px-2 text-right font-mono">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 px-2 text-center text-[#737373]">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="py-3 px-2 text-right font-mono text-[#737373]">₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="py-3 px-2 text-right font-mono font-bold text-[#252525]">₹{{ number_format($item->amount + $item->tax_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Calculation Totals Summary --}}
        <div class="flex justify-end border-t border-[#E5E5E5] pt-4">
            <div class="w-full md:w-64 space-y-2 font-mono text-xs">
                <div class="flex justify-between text-[#737373]">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-[#737373]">
                    <span>Total GST Tax:</span>
                    <span>₹{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount Applied:</span>
                        <span>- ₹{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-sm text-[#252525] pt-2 border-t border-[#E5E5E5]">
                    <span>Grand Total:</span>
                    <span>₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-semibold pt-1">
                    <span>Total Paid:</span>
                    <span>₹{{ number_format($invoice->total_amount - $invoice->balance_due, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-[#ED1C24] pt-1 border-t border-slate-200">
                    <span>Balance Due:</span>
                    <span>₹{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes & Terms --}}
        @if($invoice->notes || $invoice->terms)
            <div class="pt-4 border-t border-[#E5E5E5] grid grid-cols-2 gap-4 text-[11px] text-[#737373]">
                @if($invoice->notes)
                    <div>
                        <span class="font-bold text-[#252525] block mb-0.5">Notes:</span>
                        <p>{{ $invoice->notes }}</p>
                    </div>
                @endif
                @if($invoice->terms)
                    <div>
                        <span class="font-bold text-[#252525] block mb-0.5">Terms & Conditions:</span>
                        <p>{{ $invoice->terms }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Payments History --}}
        @if(!$invoice->payments->isEmpty())
            <div class="pt-4 border-t border-[#E5E5E5] space-y-2">
                <h4 class="font-bold text-xs text-[#252525]">Payment History</h4>
                <div class="space-y-1.5">
                    @foreach($invoice->payments as $pmt)
                        <div class="p-2.5 bg-[#F7F7F8] rounded-lg border border-[#E5E5E5] flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-[#252525]">{{ $pmt->method->label() }}</span>
                                @if($pmt->reference_number)
                                    <span class="text-[#737373] text-[11px] ml-1">(Ref: {{ $pmt->reference_number }})</span>
                                @endif
                                <span class="text-slate-400 text-[10px] block">{{ $pmt->payment_date->format('d M Y') }} {{ $pmt->receivedBy ? 'by ' . $pmt->receivedBy->name : '' }}</span>
                            </div>
                            <div class="font-mono font-bold text-emerald-700 text-xs">
                                + ₹{{ number_format($pmt->amount, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- RECORD PAYMENT MODAL --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showPaymentModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-3 mb-4">
                            <h3 class="text-base font-bold text-[#252525]">Record Payment for {{ $invoice->invoice_number }}</h3>
                            <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        <form wire:submit="recordPayment" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Payment Amount (₹) <span class="text-[#ED1C24]">*</span></label>
                                <input wire:model="paymentAmount" type="number" step="0.01" max="{{ $invoice->balance_due }}" required class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                @error('paymentAmount') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-[#252525] mb-1">Payment Date <span class="text-[#ED1C24]">*</span></label>
                                    <input wire:model="paymentDate" type="date" required class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                </div>
                                <div>
                                    <label class="block font-bold text-[#252525] mb-1">Method <span class="text-[#ED1C24]">*</span></label>
                                    <select wire:model="paymentMethod" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                                        @foreach(\App\Enums\PaymentMethod::cases() as $m)
                                            <option value="{{ $m->value }}">{{ $m->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Reference / UTR Number</label>
                                <input wire:model="referenceNumber" type="text" placeholder="e.g. UTR123456789" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Notes</label>
                                <textarea wire:model="paymentNotes" rows="2" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] p-2 px-3 text-xs text-[#252525]"></textarea>
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showPaymentModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700">
                                    Save Payment Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
