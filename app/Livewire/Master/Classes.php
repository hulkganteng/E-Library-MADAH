<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\ClassRoom;

class Classes extends Component
{
    public $showModal = false, $editing = null, $name = '', $secondary = '', $search = '';
    public $nameKey = 'name', $secondaryProp = 'major', $secondaryLabel = 'Jurusan';

    public function create()
    {
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(ClassRoom $item)
    {
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->major;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:50|unique:classes,name,' . $this->editing]);
        $item = $this->editing ? ClassRoom::findOrFail($this->editing) : new ClassRoom();
        $item->name = $this->name;
        $item->grade = $this->name;
        $item->major = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(ClassRoom $item)
    {
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => ClassRoom::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Kelas',
        ]);
    }
}
