<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Category;

class Categories extends Component
{
    public $showModal = false, $editing = null, $name = '', $secondary = '', $search = '';
    public $nameKey = 'name', $secondaryProp = 'description', $secondaryLabel = 'Deskripsi';

    public function create()
    {
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Category $item)
    {
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->description;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:100|unique:categories,name,' . $this->editing]);
        $item = $this->editing ? Category::findOrFail($this->editing) : new Category();
        $item->name = $this->name;
        $item->slug = str()->slug($this->name);
        $item->description = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Category $item)
    {
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Category::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Kategori',
        ]);
    }
}
