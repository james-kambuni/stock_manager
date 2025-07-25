<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login'); // Make sure this Blade view exists
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($user->is_admin) {
    // Superadmin
            return redirect()->intended('/master/dashboard');
        }

        if ($user->role === 'tenantadmin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/dashboard');


            // Check if user is deactivated
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
            }

            // Check if user's tenant is paused
            if ($user->tenant && $user->tenant->is_active === 0) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your tenant account is paused. Please contact support.',
                ]);
            }

            // Route based on role flags or values
            if ($user->role === 'superadmin') {
                return redirect()->intended('/master/dashboard');
            } elseif ($user->role === 'tenant_admin') {
                return redirect()->intended('/admin/dashboard');
            }

            // Default user route
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
