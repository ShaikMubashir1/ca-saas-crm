<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">WhatsApp Broadcast Engine</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Send targeted notifications, tax updates, and reminders to client groups.</p>
        </div>
        <a href="{{ route('whatsapp.inbox') }}" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-xs font-semibold text-[#252525] hover:bg-[#F7F7F8]">
            ← Back to Inbox
        </a>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('warning'))
        <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200 text-xs rounded-xl font-semibold">
            {{ session('warning') }}
        </div>
    @endif

    <form wire:submit="sendBroadcast" class="space-y-6">
        {{-- Audience Filters --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4 text-xs">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Target Audience Filters</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-[#252525] mb-1">Client Type Filter</label>
                    <select wire:model.live="filterClientType" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                        <option value="">All Client Types</option>
                        @foreach(\App\Enums\ClientType::cases() as $ct)
                            <option value="{{ $ct->value }}">{{ $ct->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#252525] mb-1">Service Type Filter</label>
                    <select wire:model.live="filterServiceType" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                        <option value="">All Services</option>
                        @foreach(\App\Enums\ServiceType::cases() as $st)
                            <option value="{{ $st->value }}">{{ strtoupper($st->value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input wire:model.live="marketingConsentOnly" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-[#252525]">Enforce Marketing Opt-In Consent</span>
                    </label>
                </div>
            </div>

            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl flex justify-between items-center text-xs text-blue-900 font-semibold">
                <span>Estimated Target Recipients:</span>
                <span class="text-sm font-bold font-mono">{{ $estimatedCount }} clients</span>
            </div>
        </div>

        {{-- Message Composition --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4 text-xs">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Broadcast Content</h3>

            <div>
                <label class="block font-bold text-[#252525] mb-1">Select Approved Template</label>
                <select wire:model.live="selectedTemplateName" class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
                    <option value="">Custom / Raw Message...</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->name }}">{{ $t->name }} ({{ $t->category->label() }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#252525] mb-1">Message Body <span class="text-[#ED1C24]">*</span></label>
                <textarea wire:model="customMessage" rows="5" placeholder="Compose broadcast message text..." required class="w-full rounded-xl border-[#E5E5E5] bg-[#F7F7F8] p-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]"></textarea>
                @error('customMessage') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm">
                Dispatch Broadcast (Mock Mode)
            </button>
        </div>
    </form>
</div>
