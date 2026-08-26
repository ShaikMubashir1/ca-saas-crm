<div>
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:text-gray-700 hover:shadow-sm transition" wire:navigate>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Credential Details</h1>
            <p class="text-sm text-gray-500 mt-1">Details for portal account under client: <span class="font-semibold text-gray-700">{{ $client->name }}</span></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm max-w-2xl">
        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 class="text-lg font-semibold text-gray-900">{{ $credential->portal_name }}</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('clients.credentials.edit', [$client->id, $credential->id]) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition" wire:navigate>
                    Edit
                </a>
            </div>
        </div>

        <dl class="space-y-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Portal / Authority</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $credential->portal_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Username</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $credential->username }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Password</dt>
                <dd class="mt-1 flex items-center gap-3">
                    <span class="text-sm text-gray-900 font-mono">
                        @if($showPassword)
                            {{ $credential->password }}
                        @else
                            ••••••••••••
                        @endif
                    </span>
                    <button wire:click="togglePassword" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition">
                        {{ $showPassword ? 'Hide' : 'Show' }} Password
                    </button>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks / Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $credential->notes ?? '—' }}</dd>
            </div>
        </dl>
    </div>
</div>
