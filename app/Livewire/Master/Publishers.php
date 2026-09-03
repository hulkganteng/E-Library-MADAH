<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Publisher;

class Publishers extends Component
{
    public $showModal = false, $editing = null, $name = '', $secondary = '', $search = '';
    public $nameKey = 'name', $secondaryProp = 'address', $secondaryLabel = 'Alamat';

    public function create()
    {
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Publisher $item)
    {
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->address;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:150|unique:publishers,name,' . $this->editing]);
        $item = $this->editing ? Publisher::findOrFail($this->editing) : new Publisher();
        $item->name = $this->name;
        $item->address = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Publisher $item)
    {
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Publisher::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Penerbit',
        ]);
    }
}
