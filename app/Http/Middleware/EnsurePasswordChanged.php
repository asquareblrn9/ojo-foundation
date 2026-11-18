<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces users flagged with must_change_password (e.g. the seeded
 * super admin, or accounts whose password was reset by an admin)
 * to set a new password before using the dashboard.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password && ! $request->routeIs('admin.password.change', 'admin.logout')) {
            return redirect()->route('admin.password.change');
        }

        return $next($request);
    }
}
