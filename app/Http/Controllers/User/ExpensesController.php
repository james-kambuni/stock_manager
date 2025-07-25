<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('tenant_id', Auth::user()->tenant_id)->latest()->get();

        return view('user.expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
       $request->validate([
    'category' => 'required|string|max:255',
    'amount' => 'required|numeric',
    'notes' => 'nullable|string',
    'date' => 'required|date',
]);

Expense::create([
    'tenant_id' => Auth::user()->tenant_id,
    'user_id' => Auth::id(),
    'category' => $request->category,
    'amount' => $request->amount,
    'notes' => $request->notes,
    'date' => $request->date,
]);
       

        return redirect()->back()->with('success', 'Expense recorded successfully.');
    }
}
