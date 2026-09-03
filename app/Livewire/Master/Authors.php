<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Author;

class Authors extends Component
{
    public $showModal = false, $editing = null, $name = '', $secondary = '', $search = '';
    public $nameKey = 'name', $secondaryProp = 'biography', $secondaryLabel = 'Biografi';

    public function create()
    {
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Author $item)
    {
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->biography;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:150|unique:authors,name,' . $this->editing]);
        $item = $this->editing ? Author::findOrFail($this->editing) : new Author();
        $item->name = $this->name;
        $item->slug = str()->slug($this->name);
        $item->biography = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Author $item)
    {
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Author::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Penulis',
        ]);
    }
}
