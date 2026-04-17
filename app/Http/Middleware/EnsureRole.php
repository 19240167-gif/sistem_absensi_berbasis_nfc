<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = collect($roles)
            ->flatMap(fn (string $role) => explode(',', $role))
            ->map(fn (string $role) => trim($role))
            ->filter()
            ->values();

        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        if ($allowedRoles->isNotEmpty() && ! in_array($user->role?->slug, $allowedRoles->all(), true)) {
            abort(403, 'Forbidden: role is not allowed to access this resource.');
        }

        return $next($request);
    }
}
