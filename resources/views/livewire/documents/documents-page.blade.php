<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Central Document Vault</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Browse, filter, and audit all tenant client documents and verification statuses.</p>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
        <div>
            <label class="block font-bold text-[#737373] text-[10px] uppercase mb-1">Search File Name</label>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
        </div>

        <div>
            <label class="block font-bold text-[#737373] text-[10px] uppercase mb-1">Filter Client</label>
            <select wire:model.live="selectedClient" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-bold text-[#737373] text-[10px] uppercase mb-1">Filter Financial Year</label>
            <select wire:model.live="selectedFY" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                <option value="">All Financial Years</option>
                @foreach($financialYears as $fy)
                    <option value="{{ $fy->id }}">{{ $fy->year_label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-bold text-[#737373] text-[10px] uppercase mb-1">Filter Status</label>
            <select wire:model.live="selectedStatus" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                <option value="">All Statuses</option>
                @foreach(\App\Enums\DocumentStatus::cases() as $st)
                    <option value="{{ $st->value }}">{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        @if($documents->isEmpty())
            <div class="text-center py-12 text-[#737373]">
                <p class="text-xs font-semibold text-[#252525]">No documents found matching criteria.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-3 px-4">Document Name</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Service & FY</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Uploaded</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($documents as $doc)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#252525]">{{ $doc->name }}</div>
                                    <div class="text-[10px] text-[#737373] font-mono">{{ $doc->original_filename }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($doc->client)
                                        <a href="{{ route('clients.show', $doc->client->id) }}" class="font-semibold text-[#252525] hover:text-[#ED1C24]">
                                            {{ $doc->client->name }}
                                        </a>
                                    @else
                                        <span class="text-[#737373]">Unassigned</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($doc->service)
                                        <span class="font-semibold text-[#252525]">{{ strtoupper($doc->service->type->value) }}</span>
                                        <span class="text-[10px] text-[#737373] block">{{ $doc->service->financialYear ? $doc->service->financialYear->year_label : '' }}</span>
                                    @else
                                        <span class="text-[#737373]">General</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-[#737373] font-medium">
                                    {{ $doc->category }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold
                                        {{ $doc->status->value === 'verified' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                        {{ $doc->status->value === 'received' ? 'bg-blue-50 text-blue-800' : '' }}
                                        {{ $doc->status->value === 'pending' ? 'bg-amber-50 text-amber-800' : '' }}
                                        {{ $doc->status->value === 'rejected' ? 'bg-red-50 text-[#ED1C24]' : '' }}
                                    ">
                                        {{ $doc->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-[#737373]">
                                    <div>{{ $doc->created_at->format('d M Y') }}</div>
                                    <div class="text-[10px]">{{ $doc->uploader ? $doc->uploader->name : '' }}</div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('documents.download', $doc->id) }}" class="text-[#ED1C24] font-bold hover:underline" target="_blank">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[#E5E5E5]">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
