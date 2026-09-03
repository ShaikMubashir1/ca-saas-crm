<div class="h-[calc(100vh-8rem)] bg-white rounded-2xl border border-[#E5E5E5] shadow-xs flex overflow-hidden">
    {{-- Left Sidebar: Conversations List --}}
    <div class="w-full md:w-80 border-r border-[#E5E5E5] flex flex-col bg-[#F7F7F8]">
        {{-- Search & Filter Header --}}
        <div class="p-3 border-b border-[#E5E5E5] space-y-2 bg-white">
            <h2 class="text-sm font-bold text-[#252525]">WhatsApp Inbox</h2>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search contact or phone..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-1.5 px-3 text-xs text-[#252525]">
            <div class="flex gap-1 text-[11px] font-semibold">
                <button wire:click="$set('selectedStatus', 'all')" class="px-2 py-0.5 rounded {{ $selectedStatus === 'all' ? 'bg-[#252525] text-white' : 'text-[#737373] hover:bg-slate-200' }}">All</button>
                <button wire:click="$set('selectedStatus', 'open')" class="px-2 py-0.5 rounded {{ $selectedStatus === 'open' ? 'bg-[#252525] text-white' : 'text-[#737373] hover:bg-slate-200' }}">Open</button>
                <button wire:click="$set('selectedStatus', 'closed')" class="px-2 py-0.5 rounded {{ $selectedStatus === 'closed' ? 'bg-[#252525] text-white' : 'text-[#737373] hover:bg-slate-200' }}">Closed</button>
            </div>
        </div>

        {{-- Conversations Feed --}}
        <div class="flex-1 overflow-y-auto divide-y divide-[#E5E5E5]">
            @if($conversations->isEmpty())
                <div class="p-6 text-center text-xs text-[#737373]">No conversations found.</div>
            @else
                @foreach($conversations as $c)
                    <div wire:click="selectConversation({{ $c->id }})" class="p-3 cursor-pointer transition-colors {{ $selectedConversationId === $c->id ? 'bg-white border-l-4 border-[#ED1C24]' : 'hover:bg-slate-100/60' }}">
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-xs text-[#252525] truncate">
                                {{ $c->client ? $c->client->name : $c->phone_number }}
                            </span>
                            <span class="text-[10px] text-[#737373]">
                                {{ $c->last_message_at ? $c->last_message_at->format('H:i') : '' }}
                            </span>
                        </div>
                        <div class="text-[11px] text-[#737373] font-mono mt-0.5">{{ $c->phone_number }}</div>
                        <div class="flex justify-between items-center mt-1 text-[10px]">
                            <span class="text-slate-500 truncate max-w-[160px]">
                                {{ $c->messages->first()?->body ?? 'No messages yet' }}
                            </span>
                            <span class="px-1.5 py-0.2 rounded font-bold uppercase text-[9px] {{ $c->status->value === 'open' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                {{ $c->status->value }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Right Pane: Conversation & Chat Box --}}
    <div class="flex-1 flex flex-col bg-white">
        @if(!$activeConversation)
            <div class="flex-1 flex items-center justify-center text-xs text-[#737373]">
                Select a conversation to start chatting.
            </div>
        @else
            {{-- Header --}}
            <div class="p-4 border-b border-[#E5E5E5] flex justify-between items-center bg-[#F7F7F8]">
                <div>
                    <h3 class="font-bold text-sm text-[#252525]">
                        {{ $activeConversation->client ? $activeConversation->client->name : $activeConversation->phone_number }}
                    </h3>
                    <div class="text-xs text-[#737373] font-mono">{{ $activeConversation->phone_number }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="toggleConversationStatus({{ $activeConversation->id }})" class="px-3 py-1 text-xs rounded-lg border border-[#E5E5E5] bg-white font-bold text-[#252525] hover:bg-slate-100">
                        Mark as {{ $activeConversation->status->value === 'open' ? 'Closed' : 'Open' }}
                    </button>
                </div>
            </div>

            @if(session()->has('warning'))
                <div class="p-2.5 bg-amber-50 text-amber-800 text-xs font-semibold border-b border-amber-200">
                    {{ session('warning') }}
                </div>
            @endif

            {{-- Messages Body --}}
            <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#FAFDFB]">
                @foreach($activeMessages as $m)
                    <div class="flex flex-col {{ $m->direction->value === 'outbound' ? 'items-end' : 'items-start' }}">
                        <div class="max-w-md p-3 rounded-2xl text-xs {{ $m->direction->value === 'outbound' ? 'bg-[#DCF8C6] text-[#252525] rounded-br-none' : 'bg-white border border-[#E5E5E5] text-[#252525] rounded-bl-none shadow-2xs' }}">
                            <p class="whitespace-pre-line">{{ $m->body }}</p>
                            <div class="mt-1 flex items-center justify-between text-[9px] text-slate-400 font-mono space-x-2">
                                <span>{{ $m->created_at->format('d M H:i') }}</span>
                                @if($m->direction->value === 'outbound')
                                    <span class="font-bold uppercase text-emerald-800">{{ $m->status->value }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Compose Bar & Template Picker --}}
            <div class="p-4 border-t border-[#E5E5E5] space-y-3 bg-white">
                <div class="flex items-center gap-2 text-xs">
                    <select wire:model.live="selectedTemplateName" wire:change="applyTemplate" class="rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-1.5 px-2 text-xs text-[#252525]">
                        <option value="">Insert Approved Template...</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->name }}">{{ $t->name }} ({{ $t->category->label() }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <textarea wire:model="messageText" rows="2" placeholder="Type a message..." class="flex-1 rounded-xl border-[#E5E5E5] bg-[#F7F7F8] p-2.5 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]"></textarea>
                    <button wire:click="sendMessage" class="px-5 py-2 rounded-xl bg-emerald-600 text-xs font-bold text-white hover:bg-emerald-700 shadow-xs">
                        Send
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
