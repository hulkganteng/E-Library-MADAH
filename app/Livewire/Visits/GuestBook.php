<?php

namespace App\Livewire\Visits;

use App\Models\GuestVisit;
use Livewire\Component;

class GuestBook extends Component
{
    public string $name = '';
    public string $visitor_type = 'umum';
    public ?string $identifier = '';
    public ?string $institution = '';
    public ?string $phone = '';
    public string $purpose = 'membaca';
    public ?string $notes = '';

    public bool $submitted = false;
    public string $lastVisitorName = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'visitor_type' => 'required|string|in:' . implode(',', array_keys(GuestVisit::$types)),
            'identifier' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'purpose' => 'required|string|in:' . implode(',', array_keys(GuestVisit::$purposes)),
            'notes' => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'visitor_type.required' => 'Kategori pengunjung wajib dipilih.',
            'purpose.required' => 'Tujuan kunjungan wajib dipilih.',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        GuestVisit::create([
            'name' => trim($validated['name']),
            'visitor_type' => $validated['visitor_type'],
            'identifier' => $validated['identifier'] ? trim($validated['identifier']) : null,
            'institution' => $validated['institution'] ? trim($validated['institution']) : null,
            'phone' => $validated['phone'] ? trim($validated['phone']) : null,
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ? trim($validated['notes']) : null,
            'visited_at' => now(),
        ]);

        $this->lastVisitorName = trim($validated['name']);
        $this->submitted = true;

        $this->reset(['name', 'identifier', 'institution', 'phone', 'notes']);
        $this->visitor_type = 'umum';
        $this->purpose = 'membaca';

        $this->dispatch('notify', [
            'message' => 'Selamat datang! Kunjungan Anda berhasil dicatat.',
            'type' => 'success',
        ]);
    }

    public function resetSuccess(): void
    {
        $this->submitted = false;
    }

    public function render()
    {
        $todayCount = GuestVisit::whereDate('visited_at', today())->count();
        $recentVisitors = GuestVisit::whereDate('visited_at', today())
            ->latest('visited_at')
            ->take(5)
            ->get();

        return view('livewire.visits.guest-book', [
            'todayCount' => $todayCount,
            'recentVisitors' => $recentVisitors,
            'types' => GuestVisit::$types,
            'purposes' => GuestVisit::$purposes,
        ]);
    }
}
