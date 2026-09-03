<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $count = 0;

    protected $listeners = ['refreshNotifications' => 'loadCount'];

    public function loadCount()
    {
        $this->count = auth()->user()?->notifications()->unread()->count() ?? 0;
    }

    public function mount()
    {
        $this->loadCount();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
