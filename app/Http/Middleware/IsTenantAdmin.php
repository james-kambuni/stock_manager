<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsTenantAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login'); // Or abort(403) if you prefer
        }

        $user = Auth::user();

        // Check if role matches
        if ($user->role !== 'tenant_admin') {
            abort(403, 'Unauthorized: Tenant Admins only.');
        }

        return $next($request);
    }
}
