<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'phone'    => 'nullable|string|max:20',
        'is_active'=> 'nullable|boolean',
    ]);

    User::create([
        'name'      => $request->name,
        'email'     => $request->email,
        'password'  => Hash::make($request->password),
        'role'      => 'user', // Force role to "user"
        'phone'     => $request->phone,
        'is_active' => $request->is_active ?? true,
        'tenant_id' => auth()->user()->tenant_id,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
}


    public function edit(User $user)
    {
        $this->authorizeUserTenant($user);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUserTenant($user);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'is_active' => $request->is_active ?? true,

        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $this->authorizeUserTenant($user);

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Password updated.');
    }

    public function destroy(User $user)
    {
        $this->authorizeUserTenant($user);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    /**
     * Ensure tenant admins can't edit users outside their tenant.
     */
    private function authorizeUserTenant(User $user)
    {
        if (auth()->user()->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
