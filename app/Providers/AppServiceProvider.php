<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\Programme;
use App\Services\ImageService;
use App\Services\SettingsService;
use App\Support\AdminNavigation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(ImageService::class);
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        // Super Admins implicitly hold every permission.
        Gate::before(fn ($user) => $user->hasRole(Role::SuperAdmin->value) ? true : null);

        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(8)->letters()->numbers());

        $this->configureRateLimiting();

        View::composer('components.site.footer', function ($view) {
            // Cache plain arrays: the cache store does not unserialize objects.
            $view->with('footerProgrammes', Cache::remember(Programme::FOOTER_CACHE_KEY, 3600, fn () => Programme::published()
                ->ordered()->limit(5)->get(['title', 'slug'])->map->only('title', 'slug')->all()));
        });

        View::composer('layouts.admin', function ($view) {
            $user = auth()->user();
            $view->with(['user' => $user, 'navigation' => AdminNavigation::for($user)]);
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('public-forms', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('newsletter', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
    }
}
