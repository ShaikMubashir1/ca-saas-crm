<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Firm Profile & Practice Settings</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Manage firm branding, professional registrations, bank accounts, and invoice defaults.</p>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="saveSettings" class="space-y-6 text-xs">
        {{-- Firm Information --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Firm Identity & Contact</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Firm Display Name <span class="text-[#ED1C24]">*</span></label>
                    <input wire:model="firm_name" type="text" required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Legal / Registered Name</label>
                    <input wire:model="legal_name" type="text" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Practice Email Address</label>
                    <input wire:model="email" type="email" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Practice Phone Number</label>
                    <input wire:model="phone" type="text" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                </div>
            </div>

            <div>
                <label class="block font-bold text-[#252525] mb-1">Office Address</label>
                <textarea wire:model="address" rows="2" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] p-2.5 text-xs text-[#252525]"></textarea>
            </div>
        </div>

        {{-- Professional Tax & Registration --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">ICAI & Tax Registrations</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-bold text-[#252525] mb-1">CA Reg / Membership No.</label>
                    <input wire:model="ca_reg_number" type="text" placeholder="e.g. 123456" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">GSTIN</label>
                    <input wire:model="gstin" type="text" placeholder="27AAAAA0000A1Z5" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">PAN</label>
                    <input wire:model="pan" type="text" placeholder="AAAAA0000A" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">TAN</label>
                    <input wire:model="tan" type="text" placeholder="AAAA00000A" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
            </div>
        </div>

        {{-- Bank & Invoicing Defaults --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Bank Details & Invoicing Defaults</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Bank Name</label>
                    <input wire:model="bank_name" type="text" placeholder="e.g. HDFC Bank" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Account Number</label>
                    <input wire:model="account_number" type="text" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">IFSC Code / UPI ID</label>
                    <input wire:model="ifsc_code" type="text" placeholder="HDFC0001234" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Default GST Rate (%)</label>
                    <input wire:model="default_gst_percent" type="number" step="0.01" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                </div>
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Invoice Prefix</label>
                    <input wire:model="invoice_prefix" type="text" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#ED1C24] text-xs font-bold text-white hover:bg-[#C9141B]">
                Save Practice Settings
            </button>
        </div>
    </form>
</div>
