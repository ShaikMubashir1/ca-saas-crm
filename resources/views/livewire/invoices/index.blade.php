<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Invoices & Billing</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Manage client billing, tax invoices, and payment collections.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#C9141B] transition-all">
            + Create Invoice
        </a>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filters --}}
    <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs flex flex-col md:flex-row gap-3 text-xs">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by invoice number or client name..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="selectedStatus" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                <option value="">All Statuses</option>
                @foreach(\App\Enums\InvoiceStatus::cases() as $st)
                    <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="selectedClient" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        @if($invoices->isEmpty())
            <div class="text-center py-12 text-[#737373]">
                <p class="text-xs font-semibold text-[#252525]">No invoices found matching criteria.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Dates</th>
                            <th class="py-3 px-4 text-right">Total (₹)</th>
                            <th class="py-3 px-4 text-right">Balance Due (₹)</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($invoices as $inv)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-[#252525]">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="text-[#ED1C24] hover:underline">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#252525]">{{ $inv->client->name }}</div>
                                    <div class="text-[10px] text-[#737373]">{{ $inv->client->client_type->label() }}</div>
                                </td>
                                <td class="py-3 px-4 text-[#737373]">
                                    <div>Issued: {{ $inv->issue_date->format('d M Y') }}</div>
                                    <div class="text-[11px] font-semibold {{ $inv->due_date && $inv->due_date->isPast() && $inv->balance_due > 0 ? 'text-[#ED1C24]' : 'text-slate-500' }}">
                                        Due: {{ $inv->due_date ? $inv->due_date->format('d M Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-[#252525]">
                                    ₹{{ number_format($inv->total_amount, 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold {{ $inv->balance_due > 0 ? 'text-[#ED1C24]' : 'text-emerald-700' }}">
                                    ₹{{ number_format($inv->balance_due, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold {{ $inv->status->badgeClass() }}">
                                        {{ $inv->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="text-[#252525] font-bold hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[#E5E5E5]">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
