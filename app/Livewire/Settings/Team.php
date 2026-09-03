<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Team extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;

    // Staff form
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $designation = '';
    public string $roleName = 'Staff';
    public string $password = 'Password123!';

    public function openAddModal(): void
    {
        $this->reset(['name', 'email', 'phone', 'designation']);
        $this->roleName = 'Staff';
        $this->showModal = true;
    }

    public function createStaff(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'designation' => 'nullable|string',
            'roleName' => 'required|string',
        ]);

        $tenantId = Auth::user()->tenant_id;

        $user = User::create([
            'tenant_id' => $tenantId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'designation' => $this->designation,
            'status' => 'active',
            'password' => Hash::make($this->password),
        ]);

        // Assign Spatie Role
        $role = Role::firstOrCreate(['name' => $this->roleName, 'guard_name' => 'web']);
        $user->assignRole($role);

        session()->flash('success', "Staff member {$user->name} added successfully as {$this->roleName}.");
        $this->showModal = false;
    }

    public function toggleStatus(int $userId): void
    {
        $tenantId = Auth::user()->tenant_id;
        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Cannot deactivate yourself.');
            return;
        }

        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $members = User::where('tenant_id', $tenantId)
            ->with(['roles', 'assignedTasks'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->paginate(10);

        return view('livewire.settings.team', [
            'members' => $members,
        ]);
    }
}
