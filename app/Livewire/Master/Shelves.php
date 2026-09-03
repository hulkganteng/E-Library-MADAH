<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Shelf;

class Shelves extends Component
{
    public $showModal = false, $editing = null, $name = '', $secondary = '', $search = '';
    public $nameKey = 'code', $secondaryProp = 'location', $secondaryLabel = 'Lokasi';

    public function create()
    {
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Shelf $item)
    {
        $this->editing = $item->id;
        $this->name = $item->code;
        $this->secondary = $item->location;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:30|unique:shelves,code,' . $this->editing]);
        $item = $this->editing ? Shelf::findOrFail($this->editing) : new Shelf();
        $item->code = strtoupper($this->name);
        $item->location = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Shelf $item)
    {
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Shelf::when($this->search, fn ($q) => $q->where('code', 'like', "%{$this->search}%")->orWhere('location', 'like', "%{$this->search}%"))->orderBy('code')->paginate(10),
            'name' => 'Rak',
        ]);
    }
}
