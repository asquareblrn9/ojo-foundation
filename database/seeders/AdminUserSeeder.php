<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates the initial Super Admin. The account is flagged so the password
 * must be changed at first sign-in. Override the defaults with
 * SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD in .env.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'admin@folaojofoundation.org');

        $user = User::firstOrCreate(['email' => $email], [
            'name' => 'Super Administrator',
            'password' => env('SEED_ADMIN_PASSWORD', 'ChangeMe@2026'),
            'job_title' => 'Website Administrator',
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([Role::SuperAdmin->value]);
    }
}
