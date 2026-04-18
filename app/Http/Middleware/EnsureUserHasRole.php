<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Day 3A: Coarse route-level role gate for surfaces that do not yet need
 * object-level authorization decisions.
 *
 * Usage: ->middleware('role:staff') or ->middleware('role:admin,supervisor')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Unauthorized: insufficient role.');
        }

        return $next($request);
    }
}
