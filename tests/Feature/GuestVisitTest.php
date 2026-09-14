<?php

namespace Tests\Feature;

use App\Livewire\Visits\GuestBook;
use App\Livewire\Visits\Index as VisitsIndex;
use App\Models\GuestVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuestVisitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@assaadah.sch.id')->firstOrFail();
    }

    private function librarian(): User
    {
        return User::where('email', 'pustakawan@assaadah.sch.id')->firstOrFail();
    }

    public function test_guest_book_page_is_publicly_accessible(): void
    {
        $this->get(route('visits.guest-book'))->assertOk();
    }

    public function test_visitor_can_submit_guest_book_form(): void
    {
        Livewire::test(GuestBook::class)
            ->set('name', 'Budi Santoso')
            ->set('visitor_type', 'umum')
            ->set('institution', 'Universitas Terbuka')
            ->set('phone', '081234567890')
            ->set('purpose', 'membaca')
            ->set('notes', 'Riset skripsi')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSet('lastVisitorName', 'Budi Santoso')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('guest_visits', [
            'name' => 'Budi Santoso',
            'visitor_type' => 'umum',
            'institution' => 'Universitas Terbuka',
            'purpose' => 'membaca',
        ]);
    }

    public function test_guest_book_form_validation(): void
    {
        Livewire::test(GuestBook::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_unauthenticated_user_cannot_access_admin_visits(): void
    {
        $this->get(route('visits.index'))->assertRedirect(route('login'));
    }

    public function test_admin_and_librarian_can_access_visits_management(): void
    {
        $this->actingAs($this->admin())
            ->get(route('visits.index'))
            ->assertOk();

        $this->actingAs($this->librarian())
            ->get(route('visits.index'))
            ->assertOk();
    }

    public function test_admin_can_create_edit_and_delete_visit(): void
    {
        $admin = $this->admin();

        // Create
        Livewire::actingAs($admin)
            ->test(VisitsIndex::class)
            ->call('create')
            ->set('name', 'Ahmad Fauzi')
            ->set('visitor_type', 'guru')
            ->set('purpose', 'tugas')
            ->set('visited_at', now()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $visit = GuestVisit::where('name', 'Ahmad Fauzi')->firstOrFail();
        $this->assertEquals('guru', $visit->visitor_type);

        // Edit
        Livewire::actingAs($admin)
            ->test(VisitsIndex::class)
            ->call('edit', $visit->id)
            ->set('institution', 'SMP Negeri 1')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('SMP Negeri 1', $visit->fresh()->institution);

        // Delete
        Livewire::actingAs($admin)
            ->test(VisitsIndex::class)
            ->call('delete', $visit->id);

        $this->assertDatabaseMissing('guest_visits', [
            'id' => $visit->id,
        ]);
    }

    public function test_admin_can_export_csv(): void
    {
        GuestVisit::create([
            'name' => 'Test Visitor',
            'visitor_type' => 'umum',
            'purpose' => 'membaca',
            'visited_at' => now(),
        ]);

        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(VisitsIndex::class)
            ->call('exportCsv')
            ->assertFileDownloaded();
    }
}
