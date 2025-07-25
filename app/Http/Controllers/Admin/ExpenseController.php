<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index()
    {
        $expenses = Expense::where('tenant_id', auth()->user()->tenant_id)->latest()->get();
        return view('admin.expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        return view('admin.expenses.create');
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        Expense::create([
            'user_id'    => auth()->id(),
            'tenant_id'  => auth()->user()->tenant_id,
            'category'   => $request->category,
            'amount'     => $request->amount,
            'notes'      => $request->notes,
            'date'       => now(), // Optionally use $request->date if form has a date picker
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully.');
    }
}
