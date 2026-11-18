<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        $this->seed(RolesAndPermissionsSeeder::class);
    })
    ->in('Feature');

/** Create a user with the given role and sign them in. */
function actingAsRole(Role $role, array $attributes = []): User
{
    // fresh(): load every column, as a real request would (strict mode flags missing attributes).
    $user = User::factory()->withRole($role)->create($attributes)->fresh();
    test()->actingAs($user);

    return $user;
}
