<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">WhatsApp & Communication Templates</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Manage provider-agnostic notification templates and dynamic variables for client outreach.</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center justify-center rounded-lg bg-[#ED1C24] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#C9141B] transition-all">
            + Create Template
        </button>
    </div>

    {{-- Alert Messages --}}
    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-xl border border-[#E5E5E5] shadow-xs flex flex-col md:flex-row gap-3 text-xs">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search templates by name or key..." class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="selectedCategory" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                <option value="">All Categories</option>
                @foreach(\App\Enums\TemplateCategory::cases() as $cat)
                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Templates Table --}}
    <div class="bg-white rounded-xl border border-[#E5E5E5] shadow-xs overflow-hidden">
        @if($templates->isEmpty())
            <div class="text-center py-12 text-[#737373]">
                <p class="text-xs font-semibold text-[#252525]">No communication templates found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E5E5E5] text-xs">
                    <thead>
                        <tr class="text-left text-[#737373] font-bold uppercase tracking-wider bg-[#F7F7F8]">
                            <th class="py-3 px-4">Template Name & Key</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Channel</th>
                            <th class="py-3 px-4">Body Preview</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($templates as $tpl)
                            <tr class="hover:bg-[#F7F7F8] transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#252525]">{{ $tpl->name }}</div>
                                    <div class="text-[10px] text-[#737373] font-mono">{{ $tpl->template_key }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-[#252525]">
                                        {{ $tpl->category->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-[#737373]">
                                    {{ $tpl->channel->label() }}
                                </td>
                                <td class="py-3 px-4 text-[#737373] max-w-xs truncate font-mono text-[11px]">
                                    {{ $tpl->body }}
                                </td>
                                <td class="py-3 px-4">
                                    <button wire:click="toggleActive({{ $tpl->id }})" class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold {{ $tpl->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $tpl->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button wire:click="editTemplate({{ $tpl->id }})" class="text-[#ED1C24] font-bold hover:underline">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[#E5E5E5]">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

    {{-- CREATE / EDIT TEMPLATE MODAL --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-[#252525]/70 backdrop-blur-xs transition-opacity" wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[#E5E5E5]">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-center border-b border-[#E5E5E5] pb-3 mb-4">
                            <h3 class="text-base font-bold text-[#252525]">{{ $editingId ? 'Edit Template' : 'Create New Communication Template' }}</h3>
                            <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-[#252525]">✕</button>
                        </div>

                        <form wire:submit="saveTemplate" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Template Name <span class="text-[#ED1C24]">*</span></label>
                                <input wire:model="name" type="text" placeholder="e.g. Document Request Alert" required class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                @error('name') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-[#252525] mb-1">Category <span class="text-[#ED1C24]">*</span></label>
                                    <select wire:model="category" class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                        @foreach(\App\Enums\TemplateCategory::cases() as $cat)
                                            <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-[#252525] mb-1">Template Key <span class="text-[#ED1C24]">*</span></label>
                                    <input wire:model="template_key" type="text" placeholder="e.g. doc_request_v1" required class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] py-2 px-3 text-xs text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]">
                                    @error('template_key') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-[#252525] mb-1">Message Body <span class="text-[#ED1C24]">*</span></label>
                                <p class="text-[11px] text-[#737373] mb-1">Use <code class="bg-slate-100 text-[#ED1C24] px-1 rounded">@{{client_name}}</code>, <code class="bg-slate-100 text-[#ED1C24] px-1 rounded">@{{firm_name}}</code>, <code class="bg-slate-100 text-[#ED1C24] px-1 rounded">@{{financial_year}}</code>, <code class="bg-slate-100 text-[#ED1C24] px-1 rounded">@{{deadline}}</code> variables.</p>
                                <textarea wire:model="body" rows="5" required class="w-full rounded-lg border-[#E5E5E5] bg-[#F7F7F8] p-3 text-xs font-mono text-[#252525] focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]"></textarea>
                                @error('body') <span class="text-[#ED1C24] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-3 border-t border-[#E5E5E5] flex justify-end gap-2">
                                <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 rounded-lg border border-[#E5E5E5] text-[#252525] font-semibold hover:bg-[#F7F7F8]">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-[#ED1C24] text-white font-bold hover:bg-[#C9141B]">
                                    Save Template
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
