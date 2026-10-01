<?php

namespace App\Livewire;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required')]
    public string $password = '';

    public function login()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
            return;
        }

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], request()->has('remember'))) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            $user = auth()->user();
            if ($user->hasRole('Siswa')) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'Siswa tidak memerlukan login. Peminjaman buku dilakukan via Komputer Perpustakaan.');
                return;
            }
            if (!$user->is_active) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'Akun Anda dinonaktifkan. Hubungi administrator.');
                return;
            }
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);
        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.login');
    }
}
