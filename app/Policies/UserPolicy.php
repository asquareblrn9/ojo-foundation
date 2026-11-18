<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

/**
 * Rules for managing administrator accounts. Only users with the
 * "manage users" permission (Super Admins) get this far, and even they
 * cannot lock themselves out or remove the last active Super Admin.
 */
class UserPolicy
{
    public function before(User $actor): ?bool
    {
        // Deliberately no blanket allow for Super Admins: the safety rules below apply to them too.
        return $actor->can(Permission::ManageUsers->value) ? null : false;
    }

    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return true;
    }

    public function update(User $actor, User $user): bool
    {
        return true;
    }

    public function changeRole(User $actor, User $user, ?string $newRole = null): bool
    {
        if ($actor->is($user)) {
            return $newRole === null || $user->hasRole($newRole);
        }

        return ! ($this->isLastSuperAdmin($user) && $newRole !== Role::SuperAdmin->value);
    }

    public function deactivate(User $actor, User $user): bool
    {
        return ! $actor->is($user) && ! ($user->is_active && $this->isLastSuperAdmin($user));
    }

    public function delete(User $actor, User $user): bool
    {
        return ! $actor->is($user) && ! $this->isLastSuperAdmin($user);
    }

    public function resetPassword(User $actor, User $user): bool
    {
        return ! $actor->is($user);
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return $user->hasRole(Role::SuperAdmin->value)
            && User::role(Role::SuperAdmin->value)->where('is_active', true)->whereKeyNot($user->id)->doesntExist();
    }
}
