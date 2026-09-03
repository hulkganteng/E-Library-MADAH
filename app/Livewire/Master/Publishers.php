<?php

namespace App\Livewire\Master;

use App\Models\Publisher;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Publishers extends Component
{
    public $showModal = false;

    public $editing = null;

    public $name = '';

    public $secondary = '';

    public $search = '';

    public $nameKey = 'name';

    public $secondaryProp = 'address';

    public $secondaryLabel = 'Alamat';

    public function create()
    {
        Gate::authorize('penerbit.create');
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Publisher $item)
    {
        Gate::authorize('penerbit.edit');
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->address;
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'penerbit.edit' : 'penerbit.create');
        $this->validate(['name' => 'required|string|max:150|unique:publishers,name,'.$this->editing]);
        $item = $this->editing ? Publisher::findOrFail($this->editing) : new Publisher;
        $item->name = $this->name;
        $item->address = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Publisher $item)
    {
        Gate::authorize('penerbit.delete');
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Publisher::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Penerbit',
            'permission' => 'penerbit',
        ]);
    }
}
