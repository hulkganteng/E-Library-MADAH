<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $actionFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $showModal = false;

    public $selectedLog = null;

    public function showDetail($id)
    {
        $this->selectedLog = Activity::find($id);
        $this->showModal = true;
    }

    public function render()
    {
        $logs = Activity::with('causer')
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->actionFilter, fn ($q) => $q->where('event', $this->actionFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(15);

        return view('livewire.audit.index', [
            'logs' => $logs,
        ]);
    }
}
