<?php

namespace App\Livewire\Members;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Teachers extends Component
{
    use WithPagination;

    public $showModal = false;

    public $editing = null;

    public $search = '';

    public $name;

    public $email;

    public $password;

    public $nip;

    public $subject;

    public $phone;

    public $address;

    public function create()
    {
        Gate::authorize('guru.create');
        $this->reset('editing', 'name', 'email', 'password', 'nip', 'subject', 'phone', 'address');
        $this->showModal = true;
    }

    public function edit(Teacher $teacher)
    {
        Gate::authorize('guru.edit');
        $this->editing = $teacher->id;
        $this->name = $teacher->user->name;
        $this->email = $teacher->user->email;
        $this->password = '';
        $this->phone = $teacher->user->phone;
        $this->fill($teacher->only('nip', 'subject', 'address'));
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'guru.edit' : 'guru.create');
        $id = $this->editing ? Teacher::findOrFail($this->editing)->user_id : null;
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => $this->editing ? 'nullable|string|min:6' : 'required|string|min:6',
            'nip' => 'nullable|string|max:30|unique:teachers,nip,'.$this->editing,
        ]);

        $user = $this->editing ? Teacher::findOrFail($this->editing)->user : new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        if ($this->password) {
            $user->password = $this->password;
        }
        $user->save();

        if ($this->editing) {
            $teacher = Teacher::findOrFail($this->editing);
        } else {
            $teacher = new Teacher(['user_id' => $user->id]);
            $user->assignRole('Guru');
        }
        $teacher->fill($this->only('nip', 'subject', 'address'));
        $teacher->save();

        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Guru disimpan.']);
    }

    public function delete(Teacher $teacher)
    {
        Gate::authorize('guru.delete');
        $teacher->user->delete();
        $this->dispatch('notify', ['message' => 'Guru dihapus.']);
    }

    public function render()
    {
        return view('livewire.members.teachers', [
            'teachers' => Teacher::with('user')
                ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")))
                ->orderByDesc('id')->paginate(10),
        ]);
    }
}
