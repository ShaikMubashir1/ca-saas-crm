<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Firm Activity Audit Trail</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Immutable timeline audit trail of all practice activities, document modifications, compliance filings, and billing events.</p>
        </div>
    </div>

    {{-- Search Filter --}}
    <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search event type, description, or client..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525]">
    </div>

    {{-- Audit Table --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
            <thead>
                <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                    <th class="py-3 px-4">Timestamp</th>
                    <th class="py-3 px-4">User</th>
                    <th class="py-3 px-4">Event Action</th>
                    <th class="py-3 px-4">Client</th>
                    <th class="py-3 px-4">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @foreach($events as $ev)
                    <tr class="hover:bg-[#F7F7F8] transition-colors">
                        <td class="py-3 px-4 font-mono text-[11px] text-[#737373]">
                            {{ $ev->created_at->format('d M Y H:i:s') }}
                        </td>
                        <td class="py-3 px-4 font-bold text-[#252525]">
                            {{ $ev->user ? $ev->user->name : 'System' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-800">
                                {{ $ev->event_type }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            {{ $ev->client ? $ev->client->name : '—' }}
                        </td>
                        <td class="py-3 px-4 text-[#737373]">
                            {{ $ev->description }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $events->links() }}
        </div>
    </div>
</div>
