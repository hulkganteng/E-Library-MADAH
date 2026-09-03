<?php

namespace App\Livewire\Master;

use App\Models\Shelf;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Shelves extends Component
{
    public $showModal = false;

    public $editing = null;

    public $name = '';

    public $secondary = '';

    public $search = '';

    public $nameKey = 'code';

    public $secondaryProp = 'location';

    public $secondaryLabel = 'Lokasi';

    public function create()
    {
        Gate::authorize('rak.create');
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Shelf $item)
    {
        Gate::authorize('rak.edit');
        $this->editing = $item->id;
        $this->name = $item->code;
        $this->secondary = $item->location;
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'rak.edit' : 'rak.create');
        $this->validate(['name' => 'required|string|max:30|unique:shelves,code,'.$this->editing]);
        $item = $this->editing ? Shelf::findOrFail($this->editing) : new Shelf;
        $item->code = strtoupper($this->name);
        $item->location = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Shelf $item)
    {
        Gate::authorize('rak.delete');
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Shelf::when($this->search, fn ($q) => $q->where('code', 'like', "%{$this->search}%")->orWhere('location', 'like', "%{$this->search}%"))->orderBy('code')->paginate(10),
            'name' => 'Rak',
            'permission' => 'rak',
        ]);
    }
}
