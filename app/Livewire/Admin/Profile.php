<?php

namespace App\Livewire\Admin;

use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Support\Concerns\WithToasts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('My profile')]
class Profile extends Component
{
    use WithFileUploads, WithToasts;

    public string $name = '';

    public string $email = '';

    public ?string $job_title = null;

    public $avatar;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->job_title = $user->job_title;
    }

    public function saveProfile(ImageService $images): void
    {
        $user = auth()->user();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($this->avatar) {
            $images->delete($user->avatar);
            $data['avatar'] = $images->storeSingle($this->avatar, 'avatars', 400);
        } else {
            unset($data['avatar']);
        }

        $user->update($data);
        $this->reset('avatar');

        $this->toast('Profile updated.');
    }

    public function updatePassword(ActivityLogger $logger): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->forceFill(['password' => Hash::make($this->password)])->saveQuietly();
        session()->regenerate();
        $logger->log('password_changed', 'Changed password', $user);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->toast('Password updated.');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
