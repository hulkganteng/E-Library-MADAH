<?php

namespace App\Livewire\Visits;

use App\Models\GuestVisit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $filterPurpose = '';
    public string $dateFilter = 'all'; // all, today, this_week, this_month, custom
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $visitor_type = 'umum';
    public ?string $identifier = null;
    public ?string $institution = null;
    public ?string $phone = null;
    public string $purpose = 'membaca';
    public ?string $notes = null;
    public ?string $visited_at = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterPurpose' => ['except' => ''],
        'dateFilter' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPurpose(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        Gate::authorize('kunjungan.create');

        $this->reset(['editingId', 'name', 'identifier', 'institution', 'phone', 'notes']);
        $this->visitor_type = 'umum';
        $this->purpose = 'membaca';
        $this->visited_at = now()->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function edit(GuestVisit $visit): void
    {
        Gate::authorize('kunjungan.edit');

        $this->editingId = $visit->id;
        $this->name = $visit->name;
        $this->visitor_type = $visit->visitor_type;
        $this->identifier = $visit->identifier;
        $this->institution = $visit->institution;
        $this->phone = $visit->phone;
        $this->purpose = $visit->purpose;
        $this->notes = $visit->notes;
        $this->visited_at = $visit->visited_at ? $visit->visited_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');

        $this->showModal = true;
    }

    public function save(): void
    {
        Gate::authorize($this->editingId ? 'kunjungan.edit' : 'kunjungan.create');

        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'visitor_type' => 'required|string|in:' . implode(',', array_keys(GuestVisit::$types)),
            'identifier' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'purpose' => 'required|string|in:' . implode(',', array_keys(GuestVisit::$purposes)),
            'notes' => 'nullable|string|max:500',
            'visited_at' => 'required|date',
        ]);

        $data = [
            'name' => trim($this->name),
            'visitor_type' => $this->visitor_type,
            'identifier' => $this->identifier ? trim($this->identifier) : null,
            'institution' => $this->institution ? trim($this->institution) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'purpose' => $this->purpose,
            'notes' => $this->notes ? trim($this->notes) : null,
            'visited_at' => Carbon::parse($this->visited_at),
        ];

        if ($this->editingId) {
            $visit = GuestVisit::findOrFail($this->editingId);
            $visit->update($data);
            $msg = 'Data kunjungan berhasil diperbarui.';
        } else {
            GuestVisit::create($data);
            $msg = 'Data kunjungan berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->dispatch('notify', ['message' => $msg, 'type' => 'success']);
    }

    public function delete(int $id): void
    {
        Gate::authorize('kunjungan.delete');

        $visit = GuestVisit::findOrFail($id);
        $visit->delete();

        $this->dispatch('notify', ['message' => 'Data kunjungan berhasil dihapus.', 'type' => 'success']);
    }

    public function exportCsv(): StreamedResponse
    {
        Gate::authorize('kunjungan.export');

        $filename = 'data-kunjungan-tamu-' . now()->format('Y-m-d_His') . '.csv';

        $query = $this->buildQuery();

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'Waktu Kunjungan',
                'Nama Lengkap',
                'Kategori Pengunjung',
                'No. Identitas / NISN / NIP',
                'Instansi / Lembaga / Kelas',
                'No. Telepon / HP',
                'Tujuan / Keperluan',
                'Catatan',
            ]);

            $query->chunk(500, function ($visits) use ($handle) {
                foreach ($visits as $v) {
                    fputcsv($handle, [
                        $v->id,
                        $v->visited_at ? $v->visited_at->format('Y-m-d H:i:s') : '',
                        $v->name,
                        $v->type_label,
                        $v->identifier ?? '-',
                        $v->institution ?? '-',
                        $v->phone ?? '-',
                        $v->purpose_label,
                        $v->notes ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildQuery()
    {
        return GuestVisit::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('identifier', 'like', "%{$this->search}%")
                        ->orWhere('institution', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, fn ($q) => $q->where('visitor_type', $this->filterType))
            ->when($this->filterPurpose, fn ($q) => $q->where('purpose', $this->filterPurpose))
            ->when($this->dateFilter === 'today', fn ($q) => $q->whereDate('visited_at', today()))
            ->when($this->dateFilter === 'this_week', fn ($q) => $q->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($this->dateFilter === 'this_month', fn ($q) => $q->whereBetween('visited_at', [now()->startOfMonth(), now()->endOfMonth()]))
            ->when($this->dateFilter === 'custom' && $this->dateFrom, fn ($q) => $q->whereDate('visited_at', '>=', $this->dateFrom))
            ->when($this->dateFilter === 'custom' && $this->dateTo, fn ($q) => $q->whereDate('visited_at', '<=', $this->dateTo))
            ->latest('visited_at');
    }

    public function render()
    {
        Gate::authorize('kunjungan.view');

        $visits = $this->buildQuery()->paginate(15);

        $stats = [
            'today' => GuestVisit::whereDate('visited_at', today())->count(),
            'this_week' => GuestVisit::whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => GuestVisit::whereBetween('visited_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'total' => GuestVisit::count(),
        ];

        return view('livewire.visits.index', [
            'visits' => $visits,
            'stats' => $stats,
            'types' => GuestVisit::$types,
            'purposes' => GuestVisit::$purposes,
        ]);
    }
}
