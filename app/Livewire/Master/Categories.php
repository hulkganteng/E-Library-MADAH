<?php

namespace App\Livewire\Master;

use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Categories extends Component
{
    public $showModal = false;

    public $editing = null;

    public $name = '';

    public $secondary = '';

    public $search = '';

    public $nameKey = 'name';

    public $secondaryProp = 'description';

    public $secondaryLabel = 'Deskripsi';

    public function create()
    {
        Gate::authorize('kategori.create');
        $this->reset('editing', 'name', 'secondary');
        $this->showModal = true;
    }

    public function edit(Category $item)
    {
        Gate::authorize('kategori.edit');
        $this->editing = $item->id;
        $this->name = $item->name;
        $this->secondary = $item->description;
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'kategori.edit' : 'kategori.create');
        $this->validate(['name' => 'required|string|max:100|unique:categories,name,'.$this->editing]);
        $item = $this->editing ? Category::findOrFail($this->editing) : new Category;
        $item->name = $this->name;
        $item->slug = str()->slug($this->name);
        $item->description = $this->secondary;
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Disimpan.']);
    }

    public function delete(Category $item)
    {
        Gate::authorize('kategori.delete');
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.master.crud', [
            'items' => Category::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy('name')->paginate(10),
            'name' => 'Kategori',
            'permission' => 'kategori',
        ]);
    }
}
