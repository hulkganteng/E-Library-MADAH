<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Users extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editing = null;
    public $search = '';
    public $filterRole = '';
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'Siswa';
    public $is_active = true;

    public function create()
    {
        Gate::authorize('pengguna.create');
        $this->reset('editing', 'name', 'email', 'password');
        $this->role = 'Siswa';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(User $user)
    {
        Gate::authorize('pengguna.edit');
        $this->editing = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->roles->first()?->name ?? 'Siswa';
        $this->is_active = (bool) $user->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'pengguna.edit' : 'pengguna.create');
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editing,
            'password' => $this->editing ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = $this->editing ? User::findOrFail($this->editing) : new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->is_active = (bool) $this->is_active;
        if ($this->password) {
            $user->password = $this->password;
        }
        $user->save();
        $user->syncRoles([$this->role]);

        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Pengguna disimpan.']);
    }

    public function toggleActive(User $user)
    {
        Gate::authorize('pengguna.edit');
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', ['message' => 'Tidak dapat menonaktifkan akun sendiri.', 'type' => 'error']);
            return;
        }
        $user->update(['is_active' => ! $user->is_active]);
        $this->dispatch('notify', ['message' => $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.']);
    }

    public function delete(User $user)
    {
        Gate::authorize('pengguna.delete');
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', ['message' => 'Tidak dapat menghapus akun sendiri.', 'type' => 'error']);
            return;
        }
        $user->delete();
        $this->dispatch('notify', ['message' => 'Pengguna dihapus.']);
    }

    public function render()
    {
        return view('livewire.admin.users', [
            'users' => User::with('roles')
                ->when($this->search, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
                ->when($this->filterRole, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $this->filterRole)))
                ->orderByDesc('id')->paginate(10),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
