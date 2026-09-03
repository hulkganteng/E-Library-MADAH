<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.audit.index', [
            'activities' => Activity::with('causer')
                ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
                ->latest()->paginate(15),
        ]);
    }
}
