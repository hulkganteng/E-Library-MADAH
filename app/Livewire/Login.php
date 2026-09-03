<?php

namespace App\Livewire;

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

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], request()->has('remember'))) {
            session()->regenerate();
            $user = auth()->user();
            if (!$user->is_active) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'Akun Anda dinonaktifkan. Hubungi administrator.');
                return;
            }
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.login');
    }
}
