<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    // Show all services
    public function index()
    {
        $services = Service::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('admin.services.index', compact('services'));
    }

    // Show add form
    public function create()
    {
        return view('admin.services.create');
    }

    // Save service
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service added successfully');
    }
}