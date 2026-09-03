<?php

namespace App\Livewire\Announcements;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Announcement;

class Index extends Component
{
    use WithPagination;

    public $showModal = false, $editing = null, $title = '', $content = '', $audience = 'semua', $is_published = true;

    public function create()
    {
        $this->reset('editing', 'title', 'content');
        $this->audience = 'semua';
        $this->is_published = true;
        $this->showModal = true;
    }

    public function edit(Announcement $item)
    {
        $this->editing = $item->id;
        $this->fill($item->only('title', 'content', 'audience', 'is_published'));
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'audience' => 'required|in:semua,siswa,guru,pustakawan,admin',
        ]);
        $item = $this->editing ? Announcement::findOrFail($this->editing) : new Announcement(['author_id' => auth()->id()]);
        $item->fill($this->only('title', 'content', 'audience', 'is_published'));
        $item->published_at = $item->published_at ?? now();
        $item->save();
        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Pengumuman disimpan.']);
    }

    public function delete(Announcement $item)
    {
        $item->delete();
        $this->dispatch('notify', ['message' => 'Dihapus.']);
    }

    public function render()
    {
        return view('livewire.announcements.index', [
            'items' => Announcement::with('author')->latest()->paginate(10),
        ]);
    }
}
