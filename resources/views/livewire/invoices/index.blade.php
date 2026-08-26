<div>
    {{-- Page Header --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Invoices & Billing</h1>
            <p class="text-xs text-[#737373] mt-1">Manage client billing, payment receipts, and outstanding invoices.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-all duration-150" wire:navigate>
            + Create Invoice
        </a>
    </div>

    {{-- Branded KPI Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Total Billed</span>
            <p class="mt-1 text-2xl font-extrabold text-[#252525]">₹0.00</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Lifetime billing</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Paid</span>
            <p class="mt-1 text-2xl font-extrabold text-emerald-600">₹0.00</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Collected payments</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider block">Outstanding</span>
            <p class="mt-1 text-2xl font-extrabold text-orange-600">₹0.00</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Pending payment</span>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-4">
            <span class="text-[10px] font-bold text-[#ED1C24] uppercase tracking-wider block">Overdue</span>
            <p class="mt-1 text-2xl font-extrabold text-[#ED1C24]">₹0.00</p>
            <span class="text-[11px] text-[#737373] mt-0.5 block">Past payment terms</span>
        </div>
    </div>

    {{-- Empty State --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs p-12 text-center">
        <div class="mx-auto h-12 w-12 rounded-full bg-[#F7F7F8] flex items-center justify-center text-slate-400 mb-3">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>
        </div>
        <h3 class="text-sm font-bold text-[#252525]">No invoices generated</h3>
        <p class="mt-1 text-xs text-[#737373] max-w-sm mx-auto">
            Create professional GST-compliant invoices for your clients and track billing records.
        </p>
        <div class="mt-4">
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white hover:bg-[#C9141B] transition-colors" wire:navigate>
                + Create Invoice
            </a>
        </div>
    </div>
</div>
