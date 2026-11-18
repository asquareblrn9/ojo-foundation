<?php

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use App\Support\AdminNavigation;

it('lets every admin role reach the dashboard and profile', function (Role $role) {
    actingAsRole($role);

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.profile'))->assertOk();
})->with(Role::cases());

it('denies dashboard access to a user without a role', function () {
    $this->actingAs(User::factory()->create()->fresh());

    $this->get(route('admin.dashboard'))->assertForbidden();
});

it('grants the super admin every permission', function () {
    $user = actingAsRole(Role::SuperAdmin);

    foreach (Permission::cases() as $permission) {
        expect($user->can($permission->value))->toBeTrue();
    }
});

it('withholds user management and the activity log from administrators', function () {
    $user = actingAsRole(Role::Administrator);

    expect($user->can(Permission::ManageUsers->value))->toBeFalse()
        ->and($user->can(Permission::ViewActivityLog->value))->toBeFalse()
        ->and($user->can(Permission::ManageSettings->value))->toBeTrue()
        ->and($user->can(Permission::ManageEnquiries->value))->toBeTrue();
});

it('limits content editors to content permissions', function () {
    $user = actingAsRole(Role::ContentEditor);

    expect($user->can(Permission::ManagePosts->value))->toBeTrue()
        ->and($user->can(Permission::ManageGallery->value))->toBeTrue()
        ->and($user->can(Permission::ManageSettings->value))->toBeFalse()
        ->and($user->can(Permission::ManageUsers->value))->toBeFalse()
        ->and($user->can(Permission::ManageEnquiries->value))->toBeFalse();
});

it('only shows navigation items for existing routes the user may access', function () {
    $user = actingAsRole(Role::ContentEditor);

    $routes = collect(AdminNavigation::for($user))->flatMap(fn ($g) => array_column($g['items'], 'route'));

    expect($routes)->toContain('admin.dashboard')
        ->not->toContain('admin.users.index')
        ->not->toContain('admin.settings');
});

it('marks admin responses as non-indexable', function () {
    $this->get(route('admin.login'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});
