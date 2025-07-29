<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsMasterAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (
            !Auth::check() ||
            !$user->is_admin ||
            $user->role !== 'superadmin'
        ) {
            abort(403, 'Access denied. Super admin only.');
        }

        return $next($request);
    }
}
