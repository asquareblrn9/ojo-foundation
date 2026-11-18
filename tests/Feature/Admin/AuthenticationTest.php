<?php

use App\Enums\Role;
use App\Livewire\Admin\Auth\ChangePassword;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\ResetPassword;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('renders the admin login page', function () {
    $this->get(route('admin.login'))->assertOk()->assertSee('Welcome back');
});

it('redirects guests away from the dashboard to the login page', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
});

it('signs in an active administrator with valid credentials', function () {
    $user = User::factory()->superAdmin()->create(['password' => 'secret-pass-1']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'secret-pass-1')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->last_login_at)->not->toBeNull();
    expect(ActivityLog::where('action', 'login')->where('user_id', $user->id)->exists())->toBeTrue();
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create(['password' => 'secret-pass-1']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('blocks deactivated accounts from signing in', function () {
    $user = User::factory()->superAdmin()->inactive()->create(['password' => 'secret-pass-1']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'secret-pass-1')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('logs out a user who is deactivated mid-session', function () {
    $user = actingAsRole(Role::Administrator);
    $user->update(['is_active' => false]);

    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

it('rate limits repeated failed login attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $_) {
        Livewire::test(Login::class)->set('email', $user->email)->set('password', 'nope')->call('login');
    }

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'nope')
        ->call('login')
        ->assertHasErrors('email')
        ->assertSee('Too many login attempts', false);
});

it('signs the user out', function () {
    actingAsRole(Role::Administrator);

    $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

it('forces a password change when required', function () {
    $user = actingAsRole(Role::SuperAdmin, ['must_change_password' => true, 'password' => 'Temp-pass-1']);

    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.password.change'));

    Livewire::test(ChangePassword::class)
        ->set('current_password', 'Temp-pass-1')
        ->set('password', 'New-secure-pass-2')
        ->set('password_confirmation', 'New-secure-pass-2')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.dashboard'));

    $user->refresh();
    expect($user->must_change_password)->toBeFalse()
        ->and(Hash::check('New-secure-pass-2', $user->password))->toBeTrue();

    $this->get(route('admin.dashboard'))->assertOk();
});

it('requires the new password to differ from the temporary one', function () {
    actingAsRole(Role::SuperAdmin, ['must_change_password' => true, 'password' => 'Temp-pass-1']);

    Livewire::test(ChangePassword::class)
        ->set('current_password', 'Temp-pass-1')
        ->set('password', 'Temp-pass-1')
        ->set('password_confirmation', 'Temp-pass-1')
        ->call('save')
        ->assertHasErrors('password');
});

it('sends a password reset link without revealing whether the account exists', function () {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test(ForgotPassword::class)->set('email', $user->email)->call('sendLink')->assertSet('sent', true);
    Livewire::test(ForgotPassword::class)->set('email', 'nobody@example.com')->call('sendLink')->assertSet('sent', true);

    Notification::assertSentTo($user, AdminResetPasswordNotification::class);
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create(['must_change_password' => true]);
    $token = Password::createToken($user);

    Livewire::withQueryParams(['email' => $user->email])
        ->test(ResetPassword::class, ['token' => $token])
        ->assertSet('email', $user->email)
        ->set('password', 'Brand-new-pass-3')
        ->set('password_confirmation', 'Brand-new-pass-3')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.login'));

    $user->refresh();
    expect(Hash::check('Brand-new-pass-3', $user->password))->toBeTrue()
        ->and($user->must_change_password)->toBeFalse();
});
