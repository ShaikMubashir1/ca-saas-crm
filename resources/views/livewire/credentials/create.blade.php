<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:text-gray-700 hover:shadow-sm transition" wire:navigate>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Add Portal Credential</h1>
            <p class="text-sm text-gray-500 mt-1">Add login details for client: <span class="font-semibold text-gray-700">{{ $client->name }}</span></p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Credential Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Portal Name -->
                <div>
                    <label for="portal_name" class="block text-sm font-medium text-gray-700">Portal / Authority *</label>
                    <select wire:model="portal_name" id="portal_name" required class="mt-1 block w-full rounded-lg border-0 py-2.5 px-3.5 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition">
                        @foreach($portalOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('portal_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input wire:model="username" type="text" id="username" required class="mt-1 block w-full rounded-lg border-0 py-2.5 px-3.5 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition">
                    @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input wire:model="password" type="password" id="password" required class="mt-1 block w-full rounded-lg border-0 py-2.5 px-3.5 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes / Remarks</label>
                <textarea wire:model="notes" id="notes" rows="3" class="mt-1 block w-full rounded-lg border-0 py-2.5 px-3.5 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition" placeholder="Any special instructions, secret questions, or secondary codes..."></textarea>
                @error('notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition" wire:navigate>
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition">
                Save Credential
            </button>
        </div>
    </form>
</div>
