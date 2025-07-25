<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->tenant && $user->tenant->status === 'paused') {
            auth()->logout(); // logout the user
            return redirect()->route('login')->withErrors(['Your tenant account is paused. Contact support.']);
        }

        return $next($request);
    }
}

