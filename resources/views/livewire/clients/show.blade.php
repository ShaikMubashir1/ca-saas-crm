<div>
    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[#E5E5E5] bg-white p-2 text-[#737373] hover:text-[#252525] hover:shadow-xs transition" wire:navigate>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">{{ $client->name }}</h1>
            <p class="text-xs text-[#737373] mt-0.5">{{ $client->entity_type }}</p>
        </div>
    </div>

    {{-- Details Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Contact Information --}}
        <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
            <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2">Contact Information</h3>
            <dl class="space-y-3.5 text-xs">
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">Name</dt>
                    <dd class="mt-0.5 font-semibold text-[#252525]">{{ $client->name }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">Email</dt>
                    <dd class="mt-0.5 font-medium text-[#252525]">{{ $client->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">Phone</dt>
                    <dd class="mt-0.5 font-medium text-[#252525]">{{ $client->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">Entity Type</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-[#252525]">
                            {{ $client->entity_type }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Tax & Identifiers --}}
        <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
            <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2">Tax & Registration Identifiers</h3>
            <dl class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">PAN</dt>
                    <dd class="mt-0.5 font-mono text-[#252525] font-semibold">{{ $client->pan ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">GSTIN</dt>
                    <dd class="mt-0.5 font-mono text-[#252525] font-semibold">{{ $client->gstin ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">TAN</dt>
                    <dd class="mt-0.5 font-mono text-[#252525] font-semibold">{{ $client->tan ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">CIN</dt>
                    <dd class="mt-0.5 font-mono text-[#252525] font-semibold">{{ $client->cin ?? '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="font-bold text-[#737373] uppercase tracking-wider text-[10px]">Aadhaar</dt>
                    <dd class="mt-0.5 font-mono text-[#252525] font-semibold">{{ $client->aadhaar ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Address --}}
    <div class="mt-6 bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
        <h3 class="text-sm font-bold text-[#252525] mb-3 border-b border-[#E5E5E5] pb-2">Address</h3>
        <p class="text-xs text-[#252525] whitespace-pre-line leading-relaxed">{{ $client->address ?? 'No address on file.' }}</p>
    </div>

    {{-- Documents Section --}}
    <div class="mt-6 bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
        <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
            <h3 class="text-sm font-bold text-[#252525]">Document Vault</h3>
            <a href="{{ route('documents.upload', $client->id) }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors" wire:navigate>
                + Upload Document
            </a>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-lg font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($documents->isEmpty())
            <div class="text-center py-8 text-[#737373]">
                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2 text-xs font-medium">No documents stored for this client yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-2.5 px-4">Title</th>
                            <th class="py-2.5 px-4">Category</th>
                            <th class="py-2.5 px-4">Upload Date</th>
                            <th class="py-2.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($documents as $doc)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4 font-bold text-[#252525]">{{ $doc->name }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-[#252525]">
                                        {{ $doc->category }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-[#737373]">{{ $doc->created_at->format('d M Y, h:i A') }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button wire:click="downloadDocument({{ $doc->id }})" class="text-[#ED1C24] hover:text-[#C9141B] font-bold">
                                        Download
                                    </button>
                                    <span class="text-[#E5E5E5]">|</span>
                                    <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Delete document?" class="text-slate-400 hover:text-[#ED1C24] font-medium">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Portal Credentials Section --}}
    <div class="mt-6 bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
        <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
            <h3 class="text-sm font-bold text-[#252525]">Portal Login Credentials</h3>
            <a href="{{ route('clients.credentials.create', $client->id) }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors" wire:navigate>
                + Add Credential
            </a>
        </div>

        @if($credentials->isEmpty())
            <div class="text-center py-8 text-[#737373]">
                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m-2 4h.01M19 21V6a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6v15m15 0H3m15 0a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0018 4.5H6a2.25 2.25 0 00-2.25 2.25v12.75a2.25 2.25 0 002.25 2.25h12z" />
                </svg>
                <p class="mt-2 text-xs font-medium">No credentials stored yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-2.5 px-4">Portal Name</th>
                            <th class="py-2.5 px-4">Username</th>
                            <th class="py-2.5 px-4">Password</th>
                            <th class="py-2.5 px-4">Created Date</th>
                            <th class="py-2.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($credentials as $cred)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4 font-bold text-[#252525]">{{ $cred->portal_name }}</td>
                                <td class="py-3 px-4 font-mono text-[#252525]">{{ $cred->username }}</td>
                                <td class="py-3 px-4 font-mono text-slate-400">••••••••</td>
                                <td class="py-3 px-4 text-[#737373]">{{ $cred->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('clients.credentials.show', [$client->id, $cred->id]) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold" wire:navigate>View</a>
                                    <span class="text-[#E5E5E5]">|</span>
                                    <a href="{{ route('clients.credentials.edit', [$client->id, $cred->id]) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold" wire:navigate>Edit</a>
                                    <span class="text-[#E5E5E5]">|</span>
                                    <button wire:click="deleteCredential({{ $cred->id }})" wire:confirm="Delete credential?" class="text-slate-400 hover:text-[#ED1C24] font-medium">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Tasks & Compliance Section --}}
    <div class="mt-6 bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
        <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
            <h3 class="text-sm font-bold text-[#252525]">Tasks & Compliance</h3>
            <a href="{{ route('tasks.create') }}?client_id={{ $client->id }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors" wire:navigate>
                + Create Task
            </a>
        </div>

        @if($tasks->isEmpty())
            <div class="text-center py-8 text-[#737373]">
                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="mt-2 text-xs font-medium">No tasks assigned to this client yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-2.5 px-4">Title</th>
                            <th class="py-2.5 px-4">Service Type</th>
                            <th class="py-2.5 px-4">Priority</th>
                            <th class="py-2.5 px-4">Status</th>
                            <th class="py-2.5 px-4">Due Date</th>
                            <th class="py-2.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4 font-bold text-[#252525]">{{ $task->title }}</td>
                                <td class="py-3 px-4 text-[#737373]">{{ $task->service_type }}</td>
                                <td class="py-3 px-4">
                                    @php $p = $task->priority; @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-bold
                                        {{ $p === 'low' ? 'bg-slate-100 text-[#737373]' : '' }}
                                        {{ $p === 'medium' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $p === 'high' ? 'bg-orange-50 text-orange-700' : '' }}
                                        {{ $p === 'urgent' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                    ">
                                        {{ ucfirst($p) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @php $ds = $task->display_status; @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-bold
                                        {{ $ds === 'pending' ? 'bg-orange-50 text-orange-700' : '' }}
                                        {{ $ds === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $ds === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                        {{ $ds === 'overdue' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                    ">
                                        {{ $ds === 'in_progress' ? 'In Progress' : ucfirst($ds) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-[#737373]">{{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold" wire:navigate>View</a>
                                    <span class="text-[#E5E5E5]">|</span>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold" wire:navigate>Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
