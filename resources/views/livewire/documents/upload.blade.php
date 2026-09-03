<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-[#E5E5E5] bg-white p-2 text-[#737373] hover:text-[#252525] hover:shadow-xs transition" wire:navigate>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Upload Document</h1>
            <p class="text-xs text-[#737373] mt-0.5">Client: <span class="font-bold text-[#252525]">{{ $client->name }}</span></p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
            <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2">Document Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <!-- Title / Name -->
                <div>
                    <label for="title" class="block font-bold text-[#252525]">Document Title <span class="text-[#ED1C24]">*</span></label>
                    <input wire:model="title" type="text" id="title" required class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all" placeholder="e.g. Form 16 Q4">
                    @error('title') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Document Type / Category -->
                <div>
                    <label for="document_type" class="block font-bold text-[#252525]">Document Category / Type <span class="text-[#ED1C24]">*</span></label>
                    <select wire:model="document_type" id="document_type" required class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('document_type') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Associated Service -->
                <div class="md:col-span-2">
                    <label for="service_id" class="block font-bold text-[#252525]">Associate with Subscribed Service (Optional)</label>
                    <select wire:model="service_id" id="service_id" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                        <option value="">General Client Document (No Specific Service)</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">
                                {{ $svc->type->label() }} — {{ $svc->financialYear ? $svc->financialYear->year_label : 'FY' }} ({{ $svc->status->label() }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- File Input -->
            <div class="mt-6 text-xs">
                <label class="block font-bold text-[#252525]">Select File <span class="text-[#ED1C24]">*</span></label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-[#E5E5E5] px-6 py-8 bg-[#F7F7F8]">
                    <div class="text-center">
                        <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div class="mt-3 flex text-xs text-[#737373] justify-center">
                            <label for="file" class="relative cursor-pointer rounded-md font-bold text-[#ED1C24] hover:underline focus-within:outline-none">
                                <span>Choose a file</span>
                                <input id="file" wire:model="file" type="file" class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop here</p>
                        </div>
                        <p class="text-[11px] text-[#737373] mt-1">PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX up to 10MB</p>
                    </div>
                </div>

                @error('file') <span class="text-[#ED1C24] text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                
                {{-- Preview State --}}
                @if ($file)
                    <div class="mt-3 p-3 bg-red-50 rounded-lg flex items-center justify-between border border-red-100">
                        <span class="text-xs text-[#ED1C24] font-semibold font-mono">Selected: {{ $file->getClientOriginalName() }}</span>
                        <span class="text-[11px] font-bold text-emerald-700">Ready to upload</span>
                    </div>
                @endif

                <div wire:loading wire:target="file" class="mt-2 text-xs text-[#ED1C24] flex items-center gap-2 font-medium">
                    <svg class="animate-spin h-4 w-4 text-[#ED1C24]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Encrypting and uploading file...
                </div>
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="flex justify-end gap-3 text-xs">
            <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-[#E5E5E5] bg-white px-4 py-2 font-semibold text-[#252525] shadow-xs hover:bg-[#F7F7F8] transition" wire:navigate>
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2 font-bold text-white shadow-sm hover:bg-[#C9141B] transition" wire:loading.attr="disabled">
                Save & Secure Document
            </button>
        </div>
    </form>
</div>
