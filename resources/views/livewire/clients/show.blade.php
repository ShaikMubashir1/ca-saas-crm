<div>
    {{-- Top Alert Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-3.5 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-3.5 bg-red-50 text-[#ED1C24] border border-red-200 text-xs rounded-xl font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center rounded-lg border border-[#E5E5E5] bg-white p-2.5 text-[#737373] hover:text-[#252525] hover:shadow-xs transition" wire:navigate title="Back to Clients">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold tracking-tight text-[#252525]">{{ $client->name }}</h1>
                    @if($client->client_type)
                        <span class="inline-flex items-center rounded-md bg-red-50 text-[#ED1C24] px-2.5 py-0.5 text-xs font-bold border border-red-100">
                            {{ $client->client_type->label() }}
                        </span>
                    @endif
                    <span class="inline-flex items-center rounded-md bg-slate-100 text-[#252525] px-2.5 py-0.5 text-xs font-semibold">
                        {{ $client->entity_type }}
                    </span>
                </div>
                <p class="text-xs text-[#737373] mt-1 font-medium">Client 360 Workspace • Created {{ $client->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="openCommunicationModal" class="inline-flex items-center justify-center rounded-lg bg-[#252525] px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-black transition-all">
                💬 Send Message
            </button>
            <button wire:click="openServiceModal" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-[#C9141B] transition-all">
                + Add Service
            </button>
        </div>
    </div>

    {{-- Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN (2 Cols): Overview & Services --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Client Overview Card --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <h3 class="text-sm font-bold text-[#252525] mb-4 border-b border-[#E5E5E5] pb-2 flex items-center justify-between">
                    <span>Client Identity & Contact</span>
                    <span class="text-[11px] font-normal text-[#737373]">Tenant ID: {{ $client->tenant_id }}</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    <div>
                        <span class="font-bold text-[#737373] uppercase tracking-wider text-[10px] block">Contact</span>
                        <div class="mt-1 space-y-1">
                            <div class="font-semibold text-[#252525]">{{ $client->phone ? $client->phone : 'No phone recorded' }}</div>
                            <div class="text-[#737373] font-medium">{{ $client->email ? $client->email : 'No email recorded' }}</div>
                        </div>
                    </div>

                    <div>
                        <span class="font-bold text-[#737373] uppercase tracking-wider text-[10px] block">Registration Identifiers</span>
                        <div class="mt-1 space-y-1 font-mono">
                            <div><span class="text-[#737373] font-sans">PAN:</span> <span class="font-semibold text-[#252525]">{{ $client->pan ? substr($client->pan, 0, 2) . '••••' . substr($client->pan, -2) : '—' }}</span></div>
                            <div><span class="text-[#737373] font-sans">GSTIN:</span> <span class="font-semibold text-[#252525]">{{ $client->gstin ?? '—' }}</span></div>
                            <div><span class="text-[#737373] font-sans">TAN:</span> <span class="font-semibold text-[#252525]">{{ $client->tan ?? '—' }}</span></div>
                            <div><span class="text-[#737373] font-sans">CIN:</span> <span class="font-semibold text-[#252525]">{{ $client->cin ?? '—' }}</span></div>
                            @if($client->aadhaar)
                                <div><span class="text-[#737373] font-sans">Aadhaar:</span> <span class="font-semibold text-[#252525]">••••••••{{ substr($client->aadhaar, -4) }}</span></div>
                            @endif
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-2 border-t border-[#F7F7F8]">
                        <span class="font-bold text-[#737373] uppercase tracking-wider text-[10px] block">Registered Address</span>
                        <p class="mt-1 text-xs text-[#252525] whitespace-pre-line leading-relaxed font-medium">{{ $client->address ?? 'No address on file.' }}</p>
                    </div>
                </div>
            </div>

            {{-- Services Section --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#252525]">Subscribed Services</h3>
                        <p class="text-[11px] text-[#737373] mt-0.5">Track tax filings, audits, and compliance retainers.</p>
                    </div>
                    <button wire:click="openServiceModal" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors">
                        + Add Service
                    </button>
                </div>

                @if($services->isEmpty())
                    <div class="text-center py-10 text-[#737373]">
                        <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-xs font-semibold text-[#252525]">No services created for this client yet.</p>
                        <p class="text-[11px] text-[#737373]">Click "+ Add Service" above to assign tax returns or audit workflows.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                            <thead>
                                <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                                    <th class="py-2.5 px-3">Service</th>
                                    <th class="py-2.5 px-3">Financial Year</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3">Assigned Staff</th>
                                    <th class="py-2.5 px-3">Reviewer</th>
                                    <th class="py-2.5 px-3">Deadline</th>
                                    <th class="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E5E5E5]">
                                @foreach($services as $svc)
                                    <tr class="hover:bg-[#F7F7F8] transition-colors">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-[#252525]">{{ $svc->type->label() }}</div>
                                            @if($svc->arn)
                                                <div class="text-[10px] font-mono text-[#737373]">ARN: {{ $svc->arn }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-[#252525]">
                                            {{ $svc->financialYear ? $svc->financialYear->year_label : 'N/A' }}
                                        </td>
                                        <td class="py-3 px-3">
                                            <select wire:change="updateServiceStatus({{ $svc->id }}, $event.target.value)" class="text-[11px] font-bold rounded-lg border-[#E5E5E5] py-1 px-2 focus:ring-[#ED1C24]
                                                {{ $svc->status->value === 'completed' || $svc->status->value === 'acknowledged' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                                {{ $svc->status->value === 'filed' || $svc->status->value === 'ready_to_file' ? 'bg-blue-50 text-blue-800' : '' }}
                                                {{ $svc->status->value === 'in_review' ? 'bg-purple-50 text-purple-800' : '' }}
                                                {{ $svc->status->value === 'docs_pending' ? 'bg-amber-50 text-amber-800' : '' }}
                                                {{ $svc->status->value === 'not_started' ? 'bg-slate-100 text-slate-700' : '' }}
                                            ">
                                                @foreach(\App\Enums\ServiceStatus::cases() as $st)
                                                    <option value="{{ $st->value }}" {{ $svc->status->value === $st->value ? 'selected' : '' }}>
                                                        {{ $st->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-3 px-3 text-[#737373] font-medium">
                                            {{ $svc->assignedStaff ? $svc->assignedStaff->name : 'Unassigned' }}
                                        </td>
                                        <td class="py-3 px-3 text-[#737373] font-medium">
                                            {{ $svc->reviewer ? $svc->reviewer->name : 'Unassigned' }}
                                        </td>
                                        <td class="py-3 px-3 font-medium text-[#737373]">
                                            {{ $svc->deadline ? $svc->deadline->format('d M Y') : '—' }}
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            @if($svc->filing_date)
                                                <span class="text-[10px] text-emerald-700 font-semibold block">Filed {{ $svc->filing_date->format('d M Y') }}</span>
                                            @else
                                                <span class="text-[10px] text-[#737373]">Active</span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Service Checklist Row --}}
                                    <tr>
                                        <td colspan="7" class="p-3 bg-[#F7F7F8]/50">
                                            <livewire:documents.checklist-manager :service="$svc" :key="'checklist-'.$svc->id" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Document Summary Section --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#252525]">Document Vault Summary</h3>
                        <p class="text-[11px] text-[#737373] mt-0.5">Files stored across client services and general repository.</p>
                    </div>
                    <a href="{{ route('documents.upload', $client->id) }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors" wire:navigate>
                        + Upload File
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-[#F7F7F8] p-3 rounded-lg border border-[#E5E5E5]">
                        <span class="text-[10px] font-bold text-[#737373] uppercase tracking-wider block">Total Stored</span>
                        <span class="text-base font-bold text-[#252525]">{{ $documents->count() }} Files</span>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Verified</span>
                        <span class="text-base font-bold text-emerald-900">{{ $documents->filter(fn($d) => $d->status === \App\Enums\DocumentStatus::VERIFIED)->count() }}</span>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-100">
                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Received / Pending</span>
                        <span class="text-base font-bold text-amber-900">{{ $documents->filter(fn($d) => in_array($d->status, [\App\Enums\DocumentStatus::RECEIVED, \App\Enums\DocumentStatus::PENDING, \App\Enums\DocumentStatus::UNDER_REVIEW]))->count() }}</span>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                        <span class="text-[10px] font-bold text-red-800 uppercase tracking-wider block">Rejected</span>
                        <span class="text-base font-bold text-[#ED1C24]">{{ $documents->filter(fn($d) => $d->status === \App\Enums\DocumentStatus::REJECTED)->count() }}</span>
                    </div>
                </div>

                @if($documents->isEmpty())
                    <div class="text-center py-6 text-[#737373]">
                        <p class="text-xs font-medium">No documents uploaded for this client yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                            <thead>
                                <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                                    <th class="py-2 px-3">File Name</th>
                                    <th class="py-2 px-3">Category</th>
                                    <th class="py-2 px-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E5E5E5]">
                                @foreach($documents->take(5) as $doc)
                                    <tr class="hover:bg-[#F7F7F8] transition-colors">
                                        <td class="py-2 px-3 font-bold text-[#252525]">{{ $doc->name }}</td>
                                        <td class="py-2 px-3 text-[#737373]">{{ $doc->category }}</td>
                                        <td class="py-2 px-3 text-right space-x-2">
                                            <a href="{{ route('documents.download', $doc->id) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold text-[11px]" target="_blank">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Tasks & Compliance Summary Section --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#252525]">Task Summary</h3>
                        <p class="text-[11px] text-[#737373] mt-0.5">Associated tasks and compliance items.</p>
                    </div>
                    <a href="{{ route('tasks.create') }}?client_id={{ $client->id }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-colors" wire:navigate>
                        + Create Task
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-100">
                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Pending</span>
                        <span class="text-base font-bold text-amber-900">{{ $tasks->where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block">In Progress</span>
                        <span class="text-base font-bold text-blue-900">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Completed</span>
                        <span class="text-base font-bold text-emerald-900">{{ $tasks->where('status', 'completed')->count() }}</span>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                        <span class="text-[10px] font-bold text-red-800 uppercase tracking-wider block">Overdue</span>
                        <span class="text-base font-bold text-[#ED1C24]">{{ $tasks->where('is_overdue', true)->count() }}</span>
                    </div>
                </div>

                @if($tasks->isEmpty())
                    <div class="text-center py-4 text-[#737373]">
                        <p class="text-xs font-medium">No tasks logged for this client yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                            <thead>
                                <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                                    <th class="py-2 px-3">Title</th>
                                    <th class="py-2 px-3">Status</th>
                                    <th class="py-2 px-3">Due Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E5E5E5]">
                                @foreach($tasks->take(5) as $tsk)
                                    <tr class="hover:bg-[#F7F7F8] transition-colors">
                                        <td class="py-2 px-3 font-bold text-[#252525]">{{ $tsk->title }}</td>
                                        <td class="py-2 px-3">
                                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-[#252525]">
                                                {{ $tsk->display_status }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 text-[#737373]">{{ $tsk->due_date ? $tsk->due_date->format('d M Y') : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN (1 Col): FY Summary, Credentials, Billing, Timeline --}}
        <div class="space-y-6">

            {{-- Financial Year Summary --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <h3 class="text-sm font-bold text-[#252525] mb-3 border-b border-[#E5E5E5] pb-2">Financial Years Overview</h3>
                @if($fySummary->isEmpty())
                    <p class="text-xs text-[#737373]">No financial years with active services.</p>
                @else
                    <div class="space-y-2.5">
                        @foreach($fySummary as $fyLabel => $svcs)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-[#F7F7F8] border border-[#E5E5E5]">
                                <span class="font-bold text-xs text-[#252525]">{{ $fyLabel }}</span>
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-bold text-[#ED1C24]">
                                    {{ $svcs->count() }} Service{{ $svcs->count() === 1 ? '' : 's' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Portal Login Credentials --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-[#E5E5E5] pb-2">
                    <h3 class="text-sm font-bold text-[#252525]">Portal Credentials</h3>
                    <a href="{{ route('clients.credentials.create', $client->id) }}" class="text-xs font-bold text-[#ED1C24] hover:underline" wire:navigate>+ Add</a>
                </div>

                @if($credentials->isEmpty())
                    <p class="text-xs text-[#737373]">No credentials saved yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($credentials as $cred)
                            <div class="p-3 bg-[#F7F7F8] rounded-lg border border-[#E5E5E5] flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-bold text-[#252525]">{{ $cred->portal_name }}</div>
                                    <div class="font-mono text-[#737373] text-[11px]">{{ $cred->username }}</div>
                                </div>
                                <a href="{{ route('clients.credentials.show', [$client->id, $cred->id]) }}" class="text-[#ED1C24] font-bold text-[11px]" wire:navigate>View</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Enhanced Billing Summary & History Card --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-2">
                    <h3 class="text-sm font-bold text-[#252525]">Billing & Payments</h3>
                    <a href="{{ route('invoices.create') }}" class="text-xs font-bold text-[#ED1C24] hover:underline">+ Create Invoice</a>
                </div>

                {{-- Billing Metrics Grid --}}
                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2 bg-[#F7F7F8] rounded-lg border border-[#E5E5E5]">
                        <span class="block text-[10px] text-[#737373] font-semibold">Invoiced</span>
                        <span class="font-bold text-xs text-[#252525]">₹{{ number_format($billingMetrics['total_invoiced'], 0) }}</span>
                    </div>
                    <div class="p-2 bg-emerald-50/50 rounded-lg border border-emerald-100">
                        <span class="block text-[10px] text-emerald-800 font-semibold">Paid</span>
                        <span class="font-bold text-xs text-emerald-900">₹{{ number_format($billingMetrics['total_paid'], 0) }}</span>
                    </div>
                    <div class="p-2 bg-amber-50/50 rounded-lg border border-amber-100">
                        <span class="block text-[10px] text-amber-800 font-semibold">Due</span>
                        <span class="font-bold text-xs text-amber-900">₹{{ number_format($billingMetrics['outstanding'], 0) }}</span>
                    </div>
                    <div class="p-2 bg-red-50/50 rounded-lg border border-red-100">
                        <span class="block text-[10px] text-[#ED1C24] font-semibold">Overdue</span>
                        <span class="font-bold text-xs text-[#ED1C24]">₹{{ number_format($billingMetrics['overdue'], 0) }}</span>
                    </div>
                </div>

                @if($invoices->isEmpty())
                    <p class="text-xs text-[#737373]">No invoices generated for this client yet.</p>
                @else
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($invoices as $inv)
                            <div class="p-2.5 bg-[#F7F7F8] rounded-xl border border-[#E5E5E5] flex justify-between items-center text-xs">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="font-mono font-bold text-[#252525] hover:text-[#ED1C24]">
                                            {{ $inv->invoice_number }}
                                        </a>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.2 text-[9px] font-bold {{ $inv->status->badgeClass() }}">
                                            {{ $inv->status->label() }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-[#737373]">Issued: {{ $inv->issue_date->format('d M Y') }}</span>
                                </div>
                                <div class="text-right font-mono">
                                    <div class="font-bold text-[#252525]">₹{{ number_format($inv->total_amount, 2) }}</div>
                                    <div class="text-[10px] {{ $inv->balance_due > 0 ? 'text-[#ED1C24] font-semibold' : 'text-emerald-700' }}">
                                        Bal: ₹{{ number_format($inv->balance_due, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Compliance Calendar Summary Card --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-2">
                    <h3 class="text-sm font-bold text-[#252525]">Compliance Filings</h3>
                    <a href="{{ route('compliance.dashboard') }}" class="text-xs font-bold text-[#ED1C24] hover:underline">View Calendar →</a>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 bg-[#F7F7F8] rounded-lg border border-[#E5E5E5]">
                        <span class="block text-[10px] text-[#737373] font-semibold">Total</span>
                        <span class="font-bold text-xs text-[#252525]">{{ $complianceMetrics['total'] }}</span>
                    </div>
                    <div class="p-2 bg-red-50/50 rounded-lg border border-red-100">
                        <span class="block text-[10px] text-[#ED1C24] font-semibold">Overdue</span>
                        <span class="font-bold text-xs text-[#ED1C24]">{{ $complianceMetrics['overdue'] }}</span>
                    </div>
                    <div class="p-2 bg-emerald-50/50 rounded-lg border border-emerald-100">
                        <span class="block text-[10px] text-emerald-800 font-semibold">Filed</span>
                        <span class="font-bold text-xs text-emerald-900">{{ $complianceMetrics['filed'] }}</span>
                    </div>
                </div>

                @if($complianceInstances->isEmpty())
                    <p class="text-xs text-[#737373]">No compliance filings generated yet.</p>
                @else
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($complianceInstances as $cInst)
                            <div class="p-2.5 bg-[#F7F7F8] rounded-xl border border-[#E5E5E5] flex justify-between items-center text-xs">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-[#252525]">{{ $cInst->template->code }}</span>
                                        <span class="text-[10px] text-[#737373]">({{ $cInst->period }})</span>
                                    </div>
                                    <div class="text-[10px] text-[#737373]">Due: {{ $cInst->due_date->format('d M Y') }}</div>
                                </div>
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[9px] font-bold {{ $cInst->status->badgeClass() }}">
                                    {{ $cInst->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Communication Summary & History Card --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-2">
                    <h3 class="text-sm font-bold text-[#252525]">Communication Overview</h3>
                    <button wire:click="openCommunicationModal" class="text-xs font-bold text-[#ED1C24] hover:underline">+ New Message</button>
                </div>

                {{-- Channel & Status Counter Grid --}}
                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2 bg-[#F7F7F8] rounded-lg border border-[#E5E5E5]">
                        <span class="block text-[10px] text-[#737373] font-semibold">Total</span>
                        <span class="font-bold text-sm text-[#252525]">{{ $commMetrics['total'] }}</span>
                    </div>
                    <div class="p-2 bg-emerald-50/50 rounded-lg border border-emerald-100">
                        <span class="block text-[10px] text-emerald-800 font-semibold">WhatsApp</span>
                        <span class="font-bold text-sm text-emerald-900">{{ $commMetrics['whatsapp'] }}</span>
                    </div>
                    <div class="p-2 bg-blue-50/50 rounded-lg border border-blue-100">
                        <span class="block text-[10px] text-blue-800 font-semibold">Email</span>
                        <span class="font-bold text-sm text-blue-900">{{ $commMetrics['email'] }}</span>
                    </div>
                    <div class="p-2 bg-purple-50/50 rounded-lg border border-purple-100">
                        <span class="block text-[10px] text-purple-800 font-semibold">Delivered</span>
                        <span class="font-bold text-sm text-purple-900">{{ $commMetrics['delivered'] }}</span>
                    </div>
                </div>

                @if($communications->isEmpty() && $communicationMessages->isEmpty())
                    <p class="text-xs text-[#737373]">No communications dispatched yet.</p>
                @else
                    <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                        @foreach($communications as $comm)
                            <div class="p-3 bg-[#F7F7F8] rounded-xl border border-[#E5E5E5] text-xs space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-[#252525]">{{ $comm->channel->label() }}</span>
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-bold {{ $comm->status->badgeClass() }}">
                                        {{ $comm->status->label() }}
                                    </span>
                                </div>
                                <p class="text-[#737373] text-[11px] font-mono whitespace-pre-line truncate">{{ $comm->message }}</p>
                                <div class="text-[10px] text-slate-400 flex justify-between pt-1 border-t border-slate-200/60">
                                    <span>{{ $comm->created_at->format('d M, H:i') }}</span>
                                    <span>{{ $comm->user ? $comm->user->name : 'System' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white p-6 rounded-xl border border-[#E5E5E5] shadow-xs">
                <h3 class="text-sm font-bold text-[#252525] mb-3 border-b border-[#E5E5E5] pb-2">Activity Timeline</h3>
                @if($timelineEvents->isEmpty())
                    <p class="text-xs text-[#737373]">No activity logged for this client.</p>
                @else
                    <div class="relative pl-4 border-l-2 border-slate-200 space-y-4">
                        @foreach($timelineEvents as $event)
                            <div class="relative text-xs">
                                <div class="absolute -left-[21px] top-1 h-2.5 w-2.5 rounded-full bg-[#ED1C24]"></div>
                                <div class="font-bold text-[#252525]">{{ $event->event_type }}</div>
                                <div class="text-[11px] text-[#737373] mt-0.5">{{ $event->description }}</div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    {{ $event->created_at->diffForHumans() }} {{ $event->user ? 'by ' . $event->user->name : '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- ADD SERVICE MODAL --}}
    @if($showServiceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="closeServiceModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-3 mb-4">
                            <h3 class="text-base font-bold text-[#252525]">Add New Service</h3>
                            <button wire:click="closeServiceModal" class="text-slate-400 hover:text-[#252525]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form wire:submit="addService" class="space-y-4 text-xs">
                            {{-- Service Type --}}
                            <div>
                                <label for="service_type" class="block font-bold text-[#252525]">Service Type <span class="text-[#ED1C24]">*</span></label>
                                <select wire:model="service_type" id="service_type" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    @foreach(\App\Enums\ServiceType::cases() as $st)
                                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                    @endforeach
                                </select>
                                @error('service_type') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Financial Year --}}
                            <div>
                                <label for="financial_year_id" class="block font-bold text-[#252525]">Financial Year <span class="text-[#ED1C24]">*</span></label>
                                <select wire:model="financial_year_id" id="financial_year_id" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    @foreach($financialYears as $fy)
                                        <option value="{{ $fy->id }}">{{ $fy->year_label }}</option>
                                    @endforeach
                                </select>
                                @error('financial_year_id') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Initial Status --}}
                            <div>
                                <label for="status" class="block font-bold text-[#252525]">Status <span class="text-[#ED1C24]">*</span></label>
                                <select wire:model="status" id="status" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    @foreach(\App\Enums\ServiceStatus::cases() as $stat)
                                        <option value="{{ $stat->value }}">{{ $stat->label() }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Assigned Staff & Reviewer --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="assigned_staff_id" class="block font-bold text-[#252525]">Assigned Staff</label>
                                    <select wire:model="assigned_staff_id" id="assigned_staff_id" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                        <option value="">Unassigned</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('assigned_staff_id') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="reviewer_id" class="block font-bold text-[#252525]">Reviewer</label>
                                    <select wire:model="reviewer_id" id="reviewer_id" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                        <option value="">Unassigned</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('reviewer_id') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_date" class="block font-bold text-[#252525]">Start Date</label>
                                    <input wire:model="start_date" type="date" id="start_date" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    @error('start_date') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="deadline" class="block font-bold text-[#252525]">Deadline</label>
                                    <input wire:model="deadline" type="date" id="deadline" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    @error('deadline') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label for="notes" class="block font-bold text-[#252525]">Notes</label>
                                <textarea wire:model="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]"></textarea>
                                @error('notes') <span class="text-[#ED1C24] text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Form Actions --}}
                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="closeServiceModal" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Save Service
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SEND COMMUNICATION MODAL --}}
    @if($showCommunicationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showCommunicationModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-3 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-[#252525]">Send WhatsApp Message</h3>
                                <p class="text-xs text-[#737373] mt-0.5">Recipient: {{ $client->name }} ({{ $client->phone ?? 'No Phone' }})</p>
                            </div>
                            <button wire:click="$set('showCommunicationModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        <form wire:submit="sendClientMessage" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Load Template (Optional)</label>
                                <select wire:model.live="selectedTemplateId" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    <option value="">Select a template...</option>
                                    @foreach($communicationTemplates as $tpl)
                                        <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->category->label() }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="messageBody" class="block font-bold text-[#252525] mb-1">Message Content <span class="text-[#ED1C24]">*</span></label>
                                <textarea wire:model="messageBody" id="messageBody" rows="5" required class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] p-3 text-xs font-mono text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]"></textarea>
                                @error('messageBody') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showCommunicationModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Dispatch Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
