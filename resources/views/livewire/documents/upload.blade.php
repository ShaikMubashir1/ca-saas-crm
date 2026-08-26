<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:text-gray-700 hover:shadow-sm transition" wire:navigate>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Upload Document</h1>
            <p class="text-sm text-gray-500 mt-1">Add a new document for client: <span class="font-semibold text-gray-700">{{ $client->name }}</span></p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Document Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title / Name -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Document Title *</label>
                    <input wire:model="title" type="text" id="title" required class="mt-1 block w-full rounded-lg border-0 py-2.5 px-3.5 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition" placeholder="e.g. FY 2025-26 PAN Card">
                    @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Document Type / Category -->
                <div>
                    <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type *</label>
                    <select wire:model="document_type" id="document_type" required class="mt-1 block w-full rounded-lg border-0 py-2.5 px-3.5 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('document_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- File Input -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700">File *</label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10 bg-gray-50/50">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                        </svg>
                        <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                            <label for="file" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500 px-2">
                                <span>Upload a file</span>
                                <input id="file" wire:model="file" type="file" class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs leading-5 text-gray-500">PDF, JPG, JPEG, PNG, XLSX, DOCX up to 10MB</p>
                    </div>
                </div>

                @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                
                {{-- Preview State --}}
                @if ($file)
                    <div class="mt-4 p-3 bg-indigo-50 rounded-lg flex items-center justify-between">
                        <span class="text-xs text-indigo-700 font-medium font-mono">Selected: {{ $file->getClientOriginalName() }}</span>
                        <span class="text-xs text-indigo-500">Ready to save</span>
                    </div>
                @endif

                <div wire:loading wire:target="file" class="mt-2 text-sm text-indigo-600 flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Uploading file to temporary storage...
                </div>
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition" wire:navigate>
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition" wire:loading.attr="disabled">
                Save Document
            </button>
        </div>
    </form>
</div>
