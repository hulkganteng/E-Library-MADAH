<?php

namespace Tests\Feature;

use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\Login;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_with_valid_credentials(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@assaadah.sch.id')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));
    }

    public function test_login_with_invalid_credentials(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@assaadah.sch.id')
            ->set('password', 'wrongpass')
            ->call('login')
            ->assertHasErrors('email');
    }
}
