<?php

namespace App\Livewire\Master;

use App\Models\ClassRoom;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Classes extends Component
{
    public $showModal = false;

    public $editing = null;

    public $name = '';

    public $secondary = '';

    public $search = '';

    public $nameKey = 'name';

    public $secondaryProp = 'major';

    public $secondaryLabel = 'Jurusan';

    public function create()
    {
        Gate::authorize('kelas.create');
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(ClassRoom $item)
    {
        Gate::authorize('kelas.edit');
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->major;
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'kelas.edit' : 'kelas.create');
        $this->validate(['name' => 'required|string|max:50|unique:classes,name,'.$this->editing]);
        $item = $this->editing ? ClassRoom::findOrFail($this->editing) : new ClassRoom;
        $item->name = $this->name;
        $item->grade = $this->name;
        $item->major = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(ClassRoom $item)
    {
        Gate::authorize('kelas.delete');
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => ClassRoom::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Kelas',
            'permission' => 'kelas',
        ]);
    }
}
