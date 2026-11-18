<?php

use App\Enums\Role;
use App\Livewire\Admin\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('updates profile details and stores an optimised avatar', function () {
    Storage::fake('public');
    $user = actingAsRole(Role::ContentEditor);

    Livewire::test(Profile::class)
        ->set('name', 'Ada Editor')
        ->set('avatar', UploadedFile::fake()->image('me.jpg', 800, 800))
        ->call('saveProfile')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    $user->refresh();
    expect($user->name)->toBe('Ada Editor')
        ->and($user->avatar)->toEndWith('.webp');
    Storage::disk('public')->assertExists($user->avatar);
});

it('rejects non-image avatar uploads', function () {
    Storage::fake('public');
    actingAsRole(Role::ContentEditor);

    Livewire::test(Profile::class)
        ->set('avatar', UploadedFile::fake()->create('script.php', 10, 'application/x-php'))
        ->call('saveProfile')
        ->assertHasErrors('avatar');
});

it('changes the password when the current password is correct', function () {
    $user = actingAsRole(Role::Administrator, ['password' => 'Old-pass-123']);

    Livewire::test(Profile::class)
        ->set('current_password', 'Old-pass-123')
        ->set('password', 'New-pass-456')
        ->set('password_confirmation', 'New-pass-456')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('New-pass-456', $user->fresh()->password))->toBeTrue();
});

it('refuses a password change with the wrong current password', function () {
    actingAsRole(Role::Administrator, ['password' => 'Old-pass-123']);

    Livewire::test(Profile::class)
        ->set('current_password', 'wrong')
        ->set('password', 'New-pass-456')
        ->set('password_confirmation', 'New-pass-456')
        ->call('updatePassword')
        ->assertHasErrors('current_password');
});
