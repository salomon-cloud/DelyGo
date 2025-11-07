<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Simple fallback middleware to replace Inertia's middleware when Inertia is removed.
 * It acts as a pass-through and exposes a couple of helper methods used by the app.
 */
class HandleInertiaRequests
{
    /** The root view used by the app layout. */
    protected $rootView = 'app';

    /** Pass-through handler so middleware pipeline continues unchanged. */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /** Return null version (no asset versioning needed now). */
    public function version(Request $request): ?string
    {
        return null;
    }

    /** Return shared props similar to what Inertia's share() provided. */
    public function share(Request $request): array
    {
        return [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
