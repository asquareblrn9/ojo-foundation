<?php

use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\ProgrammeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::controller(PageController::class)->group(function () {
    Route::get('about', 'about')->name('about');
    Route::get('get-involved', 'getInvolved')->name('get-involved');
    Route::get('contact', 'contact')->name('contact');
    Route::get('privacy-policy', 'legal')->defaults('slug', 'privacy-policy')->name('privacy');
    Route::get('terms', 'legal')->defaults('slug', 'terms')->name('terms');
});

Route::get('programmes', [ProgrammeController::class, 'index'])->name('programmes.index');
Route::get('programmes/{programme:slug}', [ProgrammeController::class, 'show'])->name('programmes.show');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('blog/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('gallery/{album:slug}', [GalleryController::class, 'album'])->name('gallery.album');

Route::get('newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
