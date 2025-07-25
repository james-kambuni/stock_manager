<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserBelongsToTenant
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->tenant_id) {
            abort(403, 'You do not belong to any tenant.');
        }

        return $next($request);
    }
}
