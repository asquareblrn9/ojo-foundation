<?php

namespace App\Livewire\Admin\Users;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Users')]
class Index extends Component
{
    use RequiresPermission, WithToasts;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** Temporary password shown once after a reset. */
    public ?array $issuedPassword = null;

    protected function permission(): Permission
    {
        return Permission::ManageUsers;
    }

    public function toggleActive(int $id, ActivityLogger $logger): void
    {
        $user = User::findOrFail($id);

        if ($user->is_active) {
            $this->authorize('deactivate', $user);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $logger->log($user->is_active ? 'activated' : 'deactivated', ($user->is_active ? 'Reactivated' : 'Deactivated')." account {$user->email}", $user);
        $this->toast($user->is_active ? 'Account reactivated.' : 'Account deactivated. The user has been signed out.');
    }

    public function resetPassword(int $id, ActivityLogger $logger): void
    {
        $user = User::findOrFail($id);
        $this->authorize('resetPassword', $user);

        $password = Str::password(14, symbols: false);
        $user->forceFill(['password' => $password, 'must_change_password' => true, 'remember_token' => Str::random(60)])->saveQuietly();
        $logger->log('password_reset', "Reset password for {$user->email}", $user);

        $this->issuedPassword = ['email' => $user->email, 'password' => $password];
    }

    public function delete(int $id, ActivityLogger $logger): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->update(['is_active' => false]);
        $user->delete();
        $logger->log('deleted', "Deleted account {$user->email}", $user);
        $this->toast('Account deleted.');
    }

    public function render()
    {
        $term = trim($this->search);

        return view('livewire.admin.users.index', [
            'users' => User::with('roles')
                ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")))
                ->orderByDesc('is_active')->orderBy('name')
                ->get(),
            'roleColors' => [Role::SuperAdmin->value => 'gold', Role::Administrator->value => 'navy', Role::ContentEditor->value => 'sky'],
        ]);
    }
}
