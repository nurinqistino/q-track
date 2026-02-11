<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    /**
     * Handle an incoming request.
     * Staff includes both 'staff' role and 'admin' (admin has full access).
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        if (! in_array($user->role, [User::ROLE_STAFF, User::ROLE_ADMIN])) {
            abort(403, 'Unauthorized. Staff access required.');
        }

        return $next($request);
    }
}
