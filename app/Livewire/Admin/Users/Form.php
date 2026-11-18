<?php

namespace App\Livewire\Admin\Users;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.admin')]
class Form extends Component
{
    use RequiresPermission, WithToasts;

    #[Locked]
    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $job_title = '';

    public string $role = '';

    public bool $is_active = true;

    /** Temporary password shown once after creating the account. */
    public ?string $issuedPassword = null;

    protected function permission(): Permission
    {
        return Permission::ManageUsers;
    }

    public function mount(?User $user = null): void
    {
        $this->role = Role::ContentEditor->value;

        if ($user?->exists) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->job_title = (string) $user->job_title;
            $this->role = $user->roles->first()?->name ?? Role::ContentEditor->value;
            $this->is_active = $user->is_active;
        }
    }

    public function save(ActivityLogger $logger): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($this->userId)->withoutTrashed()],
            'job_title' => ['nullable', 'string', 'max:120'],
            'role' => ['required', Rule::enum(Role::class)],
            'is_active' => ['boolean'],
        ]);

        $user = $this->userId ? User::findOrFail($this->userId) : null;

        if ($user) {
            $this->authorize('update', $user);
            if (! $user->hasRole($this->role)) {
                $this->authorize('changeRole', [$user, $this->role]);
            }
            if ($user->is_active && ! $this->is_active) {
                $this->authorize('deactivate', $user);
            }

            $user->update(['name' => $this->name, 'email' => $this->email, 'job_title' => $this->job_title ?: null, 'is_active' => $this->is_active]);
            if (! $user->hasRole($this->role)) {
                $user->syncRoles([$this->role]);
                $logger->log('role_changed', "Changed role of {$user->email} to ".Role::from($this->role)->label(), $user);
            }

            $this->flashToast('User updated.');
            $this->redirectRoute('admin.users.index', navigate: true);

            return;
        }

        $this->authorize('create', User::class);

        $password = Str::password(14, symbols: false);
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'job_title' => $this->job_title ?: null,
            'password' => $password,
            'is_active' => $this->is_active,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);
        $user->syncRoles([$this->role]);
        $logger->log('created', "Created account {$user->email} (".Role::from($this->role)->label().')', $user);

        $this->userId = $user->id;
        $this->issuedPassword = $password;
    }

    public function render()
    {
        $user = $this->userId ? User::find($this->userId) : null;

        return view('livewire.admin.users.form', [
            'roles' => Role::options(),
            'isSelf' => $user?->is(auth()->user()) ?? false,
        ])->title($this->userId && ! $this->issuedPassword ? 'Edit user' : 'Add user');
    }
}
