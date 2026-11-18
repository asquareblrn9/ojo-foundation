<?php

namespace App\Livewire\Admin\Auth;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Mandatory password change screen for accounts flagged must_change_password. */
#[Layout('layouts.auth')]
#[Title('Change your password')]
class ChangePassword extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        if (! auth()->user()->must_change_password) {
            return $this->redirectRoute('admin.profile');
        }
    }

    public function save(ActivityLogger $logger): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->forceFill(['password' => Hash::make($this->password), 'must_change_password' => false])->saveQuietly();
        session()->regenerate();

        $logger->log('password_changed', 'Changed password (required)', $user);
        session()->flash('toast', ['type' => 'success', 'message' => 'Password updated. Welcome to your dashboard.']);

        $this->redirectRoute('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.admin.auth.change-password');
    }
}
