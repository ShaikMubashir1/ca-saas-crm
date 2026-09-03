<?php

namespace App\Livewire\Communication;

use App\Models\CommunicationTemplate;
use App\Enums\CommunicationChannel;
use App\Enums\TemplateCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TemplatesPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = '';

    // Modal state
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $category = 'utility';
    public string $channel = 'whatsapp';
    public string $template_key = '';
    public string $body = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'channel' => 'required|string',
            'template_key' => 'required|string|max:255',
            'body' => 'required|string',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editTemplate(int $id): void
    {
        $tenantId = Auth::user()->tenant_id;
        $tpl = CommunicationTemplate::where('tenant_id', $tenantId)->findOrFail($id);

        $this->editingId = $tpl->id;
        $this->name = $tpl->name;
        $this->category = $tpl->category->value;
        $this->channel = $tpl->channel->value;
        $this->template_key = $tpl->template_key;
        $this->body = $tpl->body;
        $this->showModal = true;
    }

    public function saveTemplate(): void
    {
        $this->validate();
        $tenantId = Auth::user()->tenant_id;

        // Auto extract {{variables}}
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $this->body, $matches);
        $variables = array_values(array_unique($matches[1] ?? []));

        CommunicationTemplate::updateOrCreate(
            ['id' => $this->editingId, 'tenant_id' => $tenantId],
            [
                'tenant_id' => $tenantId,
                'name' => $this->name,
                'category' => $this->category,
                'channel' => $this->channel,
                'template_key' => $this->template_key,
                'body' => $this->body,
                'variables' => $variables,
                'status' => 'approved',
                'is_active' => true,
            ]
        );

        session()->flash('success', 'Communication template saved successfully.');
        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $tenantId = Auth::user()->tenant_id;
        $tpl = CommunicationTemplate::where('tenant_id', $tenantId)->findOrFail($id);
        $tpl->is_active = !$tpl->is_active;
        $tpl->save();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->name = '';
        $this->category = 'utility';
        $this->channel = 'whatsapp';
        $this->template_key = '';
        $this->body = '';
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $templates = CommunicationTemplate::where('tenant_id', $tenantId)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('template_key', 'like', "%{$this->search}%"))
            ->when($this->selectedCategory, fn($q) => $q->where('category', $this->selectedCategory))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.communication.templates-page', [
            'templates' => $templates,
        ]);
    }
}

