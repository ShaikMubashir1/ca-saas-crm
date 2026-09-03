<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[#E5E5E5] bg-white p-2 text-[#737373] hover:text-[#252525] hover:shadow-xs transition" wire:navigate>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Add Client</h1>
            <p class="text-xs text-[#737373] mt-1">Register a new client entity with tax and registration details.</p>
        </div>
    </div>

    <form wire:submit="store" class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
            <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2">Basic Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-[#252525]">Client Name <span class="text-[#ED1C24]">*</span></label>
                    <input wire:model="name" type="text" id="name" required class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('name') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Entity Type -->
                <div>
                    <label for="entity_type" class="block text-xs font-bold text-[#252525]">Entity Type <span class="text-[#ED1C24]">*</span></label>
                    <select wire:model="entity_type" id="entity_type" required class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        <option value="Individual">Individual</option>
                        <option value="Proprietorship">Proprietorship</option>
                        <option value="Partnership Firm">Partnership Firm</option>
                        <option value="LLP">LLP</option>
                        <option value="Private Limited Company">Private Limited Company</option>
                        <option value="Public Limited Company">Public Limited Company</option>
                        <option value="HUF">HUF</option>
                        <option value="Trust">Trust</option>
                    </select>
                    @error('entity_type') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Client Type -->
                <div>
                    <label for="client_type" class="block text-xs font-bold text-[#252525]">Client Classification / Workflow Type <span class="text-[#ED1C24]">*</span></label>
                    <select wire:model="client_type" id="client_type" required class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        @foreach(\App\Enums\ClientType::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('client_type') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-[#252525]">Email Address</label>
                    <input wire:model="email" type="email" id="email" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('email') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-[#252525]">Phone Number</label>
                    <input wire:model="phone" type="text" id="phone" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('phone') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
            <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2">Tax & Registration Identifiers</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- PAN -->
                <div>
                    <label for="pan" class="block text-xs font-bold text-[#252525]">PAN (ABCDE1234F)</label>
                    <input wire:model="pan" type="text" id="pan" placeholder="ABCDE1234F" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono uppercase focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('pan') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Aadhaar -->
                <div>
                    <label for="aadhaar" class="block text-xs font-bold text-[#252525]">Aadhaar (12 Digits)</label>
                    <input wire:model="aadhaar" type="text" id="aadhaar" placeholder="123456789012" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('aadhaar') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- GSTIN -->
                <div>
                    <label for="gstin" class="block text-xs font-bold text-[#252525]">GSTIN (15 Chars)</label>
                    <input wire:model="gstin" type="text" id="gstin" placeholder="27AAAAA1111A1Z1" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono uppercase focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('gstin') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- TAN -->
                <div>
                    <label for="tan" class="block text-xs font-bold text-[#252525]">TAN (ABCD12345E)</label>
                    <input wire:model="tan" type="text" id="tan" placeholder="ABCD12345E" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono uppercase focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('tan') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- CIN -->
                <div>
                    <label for="cin" class="block text-xs font-bold text-[#252525]">CIN (21 Chars)</label>
                    <input wire:model="cin" type="text" id="cin" placeholder="U12345MH2026PTC123456" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] font-mono uppercase focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                    @error('cin') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
            <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2">Address</h3>
            
            <div>
                <label for="address" class="block text-xs font-bold text-[#252525]">Address Details</label>
                <textarea wire:model="address" id="address" rows="3" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all"></textarea>
                @error('address') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[#E5E5E5] bg-white px-4 py-2 text-xs font-semibold text-[#252525] shadow-xs hover:bg-[#F7F7F8] transition-colors" wire:navigate>
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors">
                Save Client
            </button>
        </div>
    </form>
</div>
