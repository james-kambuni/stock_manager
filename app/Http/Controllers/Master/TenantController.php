<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();
        return view('master.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('master.tenants.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'tenant_name' => 'required|string|max:255|unique:tenants,name',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'phone' => 'required|string|max:20',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
        'is_active' => 'nullable|boolean',
    ]);

    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
    }

    $tenant = Tenant::create([
        'name' => $request->tenant_name,
        'slug' => Str::slug($request->tenant_name),
        'email' => $request->email,
        'phone' => $request->phone,
        'logo' => $logoPath,
        'is_active' => $request->boolean('is_active', true),
    ]);

    User::create([
        'name' => $request->tenant_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => 'tenant_admin',
        'tenant_id' => $tenant->id,
        'is_active' => $tenant->is_active,
    ]);

    return redirect()->route('master.tenants')->with('success', 'Tenant and admin created successfully.');
}


    public function toggle($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->is_active = !$tenant->is_active;
        $tenant->save();

        $tenant->users()->update([
            'is_active' => $tenant->is_active,
        ]);

        return back()->with('success', 'Tenant and its users status updated.');
    }

    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        return back()->with('success', 'Tenant deleted.');
    }

    public function data($id)
    {
        $products = Product::withoutGlobalScopes()->where('tenant_id', $id)->get();
        $sales = Sale::withoutGlobalScopes()->where('tenant_id', $id)->get();
        $purchases = Purchase::withoutGlobalScopes()->where('tenant_id', $id)->get();

        return view('master.tenants.data', compact('id', 'products', 'sales', 'purchases'));
    }

    public function impersonate($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = User::where('tenant_id', $tenant->id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No user found to impersonate.');
        }

        session(['impersonate' => Auth::id()]);
        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Now impersonating tenant.');
    }

    public function stopImpersonate()
    {
        if (session()->has('impersonate')) {
            $superadminId = session()->pull('impersonate');
            Auth::loginUsingId($superadminId);

            return redirect()->route('master.dashboard')->with('success', 'Stopped impersonation.');
        }

        return redirect()->back()->with('error', 'Not impersonating anyone.');
    }
}
