<?php

namespace App\Livewire\Master;

use App\Models\Author;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Authors extends Component
{
    public $showModal = false;

    public $editing = null;

    public $name = '';

    public $secondary = '';

    public $search = '';

    public $nameKey = 'name';

    public $secondaryProp = 'biography';

    public $secondaryLabel = 'Biografi';

    public function create()
    {
        Gate::authorize('penulis.create');
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Author $item)
    {
        Gate::authorize('penulis.edit');
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->biography;
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'penulis.edit' : 'penulis.create');
        $this->validate(['name' => 'required|string|max:150|unique:authors,name,'.$this->editing]);
        $item = $this->editing ? Author::findOrFail($this->editing) : new Author;
        $item->name = $this->name;
        $item->slug = str()->slug($this->name);
        $item->biography = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Author $item)
    {
        Gate::authorize('penulis.delete');
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Author::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Penulis',
            'permission' => 'penulis',
        ]);
    }
}
