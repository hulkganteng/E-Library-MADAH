<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;

class Notifications extends Component
{
    public function markAllRead()
    {
        auth()->user()->notifications()->update(['is_read' => true]);
        $this->dispatch('notify', ['message' => 'Semua dibaca.']);
    }

    public function delete($id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.notifications.index', [
            'notifications' => auth()->user()->notifications()->latest()->paginate(15),
        ]);
    }
}
