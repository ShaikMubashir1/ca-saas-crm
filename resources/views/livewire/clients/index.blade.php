<div>
    {{-- Page Header --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Clients</h1>
            <p class="text-xs text-[#737373] mt-1">Manage and track your client accounts, entities, and credential details.</p>
        </div>
        <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#C9141B] transition-all duration-150" wire:navigate>
            + Add Client
        </a>
    </div>

    {{-- Compact Filters Toolbar --}}
    <div class="mb-6 bg-white p-3.5 rounded-xl border border-[#E5E5E5] shadow-xs flex flex-col md:flex-row md:items-center gap-3">
        <div class="flex-1 relative">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search by name, email, phone or GSTIN..." class="block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] placeholder-[#737373] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
        </div>

        <div class="w-full md:w-56">
            <select wire:model.live="entityType" class="block w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] focus:bg-white transition-all">
                <option value="">All Entity Types</option>
                <option value="Individual">Individual</option>
                <option value="HUF">HUF</option>
                <option value="Company">Company</option>
                <option value="Firm">Firm</option>
                <option value="LLP">LLP</option>
                <option value="Trust">Trust</option>
            </select>
        </div>
    </div>

    {{-- Client Table / Empty State --}}
    <div class="bg-white shadow-xs rounded-xl border border-[#E5E5E5] overflow-hidden">
        @if($clients->isEmpty())
            <div class="p-12 text-center">
                <div class="mx-auto h-12 w-12 rounded-full bg-[#F7F7F8] flex items-center justify-center text-slate-400 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#252525]">No clients found</h3>
                <p class="mt-1 text-xs text-[#737373] max-w-sm mx-auto">
                    @if(!empty($search) || !empty($entityType))
                        No client accounts match your filter criteria. Try adjusting your search query.
                    @else
                        Get started by adding your first client account to your workspace database.
                    @endif
                </p>
                @if(empty($search) && empty($entityType))
                    <div class="mt-4">
                        <a href="{{ route('clients.create') }}" class="inline-flex items-center rounded-lg bg-[#ED1C24] px-4 py-2.5 text-xs font-semibold text-white hover:bg-[#C9141B] transition-colors" wire:navigate>
                            + Add Client
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th scope="col" class="py-3 px-4">Name</th>
                            <th scope="col" class="px-4 py-3">Entity Type</th>
                            <th scope="col" class="px-4 py-3">PAN</th>
                            <th scope="col" class="px-4 py-3">Phone</th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($clients as $client)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-[#252525]">{{ $client->name }}</div>
                                    @if($client->email)
                                        <div class="text-[11px] text-[#737373] font-medium">{{ $client->email }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-[#252525]">
                                        {{ $client->entity_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-[#737373]">
                                    {{ $client->pan ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3.5 text-[#737373]">
                                    {{ $client->phone ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('clients.show', $client) }}" class="text-[#ED1C24] hover:text-[#C9141B] font-bold" wire:navigate>View Details →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($clients->hasPages())
                <div class="border-t border-[#E5E5E5] px-4 py-3">
                    {{ $clients->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
