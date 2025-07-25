@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>All Expenses</h4>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">+ Add Expense</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($expenses->count())
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Amount (KSh)</th>
                    <th>Notes</th>
                    <th>Date</th>
                    <th>Added By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $index => $expense)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $expense->category ?? 'N/A' }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->notes ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</td>
                        <td>{{ $expense->user->name ?? 'Unknown' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No expenses recorded yet.</p>
    @endif
</div>
@endsection
