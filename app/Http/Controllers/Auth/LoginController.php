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
        return view('auth.login'); 
    }

    // Handle login
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        // Deactivated user check
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // Inactive tenant check
        if ($user->tenant && !$user->tenant->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your tenant account is paused.']);
        }

        // ✅ Custom Redirect Based on Role
        if ($user->superadmin) {
            return redirect('/master/dashboard');
        } elseif ($user->role === 'tenant_admin') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/dashboard'); 
        }
    }

    // Failed login
    return back()->withErrors([
        'email' => 'Invalid email or password.',
    ])->onlyInput('email');
}


    // Logout method
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
