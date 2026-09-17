<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 403);

        $allowed = collect($roles)->contains(
            fn (string $role) => $user->role === UserRole::from($role)
        );

        abort_unless($allowed, 403, 'You are not authorized to view this page.');

        return $next($request);
    }
}
