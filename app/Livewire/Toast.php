<?php

namespace App\Livewire;

use Livewire\Component;

class Toast extends Component
{
    public $visible = false;
    public $message = '';
    public $type = 'success';

    protected $listeners = ['notify' => 'show'];

    public function show($payload)
    {
        $this->message = is_array($payload) ? ($payload['message'] ?? '') : $payload;
        $this->type = is_array($payload) ? ($payload['type'] ?? 'success') : 'success';
        $this->visible = true;
        $this->dispatch('autoHideToast');
    }

    public function render()
    {
        return view('livewire.toast');
    }
}
