<?php

use App\Enums\Permission;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\LogoutController;
use App\Livewire\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin routes — prefixed with /admin and named admin.*
|--------------------------------------------------------------------------
| Every Livewire admin component also re-checks its permission on each
| request (RequiresPermission trait), not just on the initial page load.
*/

Route::middleware('guest')->group(function () {
    Route::livewire('login', Admin\Auth\Login::class)->name('login');
    Route::livewire('forgot-password', Admin\Auth\ForgotPassword::class)->name('password.request');
    Route::livewire('reset-password/{token}', Admin\Auth\ResetPassword::class)->name('password.reset');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');
    Route::livewire('change-password', Admin\Auth\ChangePassword::class)->name('password.change');

    Route::middleware(['password.changed', 'permission:'.Permission::AccessDashboard->value])->group(function () {
        Route::livewire('/', Admin\Dashboard::class)->name('dashboard');
        Route::livewire('profile', Admin\Profile::class)->name('profile');

        // Blog & events
        Route::middleware('permission:'.Permission::ManagePosts->value)->group(function () {
            Route::livewire('posts', Admin\Posts\Index::class)->name('posts.index');
            Route::livewire('posts/create', Admin\Posts\Form::class)->name('posts.create');
            Route::livewire('posts/{post:id}/edit', Admin\Posts\Form::class)->name('posts.edit')->withTrashed();
            Route::livewire('categories', Admin\Posts\Taxonomy::class)->name('categories');
        });

        // Programmes
        Route::middleware('permission:'.Permission::ManageProgrammes->value)->group(function () {
            Route::livewire('programmes', Admin\Programmes\Index::class)->name('programmes.index');
            Route::livewire('programmes/create', Admin\Programmes\Form::class)->name('programmes.create');
            Route::livewire('programmes/{programme:id}/edit', Admin\Programmes\Form::class)->name('programmes.edit');
        });

        // Gallery
        Route::middleware('permission:'.Permission::ManageGallery->value)->group(function () {
            Route::livewire('gallery', Admin\Gallery\Images::class)->name('gallery.images');
            Route::livewire('gallery/albums', Admin\Gallery\Albums::class)->name('gallery.albums');
            Route::livewire('gallery/categories', Admin\Gallery\Categories::class)->name('gallery.categories');
        });

        // Pages & team
        Route::middleware('permission:'.Permission::ManagePages->value)->group(function () {
            Route::livewire('pages', Admin\Pages\Index::class)->name('pages.index');
            Route::livewire('pages/{page:id}', Admin\Pages\Edit::class)->name('pages.edit');
            Route::livewire('team', Admin\Team::class)->name('team');
        });

        // Enquiries
        Route::middleware('permission:'.Permission::ManageEnquiries->value)->group(function () {
            Route::livewire('messages', Admin\Inbox\Messages::class)->name('messages.index');
            Route::livewire('messages/{message}', Admin\Inbox\MessageShow::class)->name('messages.show');
            Route::livewire('volunteers', Admin\Inbox\Volunteers::class)->name('volunteers.index');
            Route::livewire('volunteers/{application}', Admin\Inbox\VolunteerShow::class)->name('volunteers.show');
            Route::livewire('partnerships', Admin\Inbox\Partnerships::class)->name('partnerships.index');
            Route::livewire('partnerships/{enquiry}', Admin\Inbox\PartnershipShow::class)->name('partnerships.show');
            Route::get('export/volunteers', [ExportController::class, 'volunteers'])->name('volunteers.export');
            Route::get('export/partnerships', [ExportController::class, 'partnerships'])->name('partnerships.export');
            Route::get('export/messages', [ExportController::class, 'messages'])->name('messages.export');
        });

        Route::middleware('permission:'.Permission::ManageSubscribers->value)->group(function () {
            Route::livewire('subscribers', Admin\Subscribers::class)->name('subscribers');
            Route::get('export/subscribers', [ExportController::class, 'subscribers'])->name('subscribers.export');
        });

        // System
        Route::livewire('settings', Admin\Settings::class)->name('settings')
            ->middleware('permission:'.Permission::ManageSettings->value);

        Route::middleware('permission:'.Permission::ManageUsers->value)->group(function () {
            Route::livewire('users', Admin\Users\Index::class)->name('users.index');
            Route::livewire('users/create', Admin\Users\Form::class)->name('users.create');
            Route::livewire('users/{user}/edit', Admin\Users\Form::class)->name('users.edit');
        });

        Route::livewire('activity', Admin\ActivityLog::class)->name('activity')
            ->middleware('permission:'.Permission::ViewActivityLog->value);
    });
});
