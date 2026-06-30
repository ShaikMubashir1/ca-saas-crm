<div class="p-6 bg-white rounded-xl shadow">
    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Document Name</label>
            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter document name" />
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <input type="text" wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            @error('category') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">File</label>
            <input type="file" wire:model="document" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            @error('document') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            <div wire:loading wire:target="document" class="mt-2 text-sm text-gray-500">Uploading…</div>
        </div>
        <div class="flex items-center justify-end space-x-3">
            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded" wire:click="$emit('closeModal')">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700" wire:loading.attr="disabled">Upload</button>
        </div>
    </form>
</div>
