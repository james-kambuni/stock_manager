@extends('layouts.admin') {{-- Use your admin layout --}}

@section('content')
<div class="container mt-4">
    <h4>Add New Expense</h4>

    {{-- Display validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Display success message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.expenses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="category" class="form-label">Expense Category</label>
            <select name="category" class="form-select" required>
                <option value="">-- Select Expense Category --</option>
                <option value="Rent" {{ old('category') == 'Rent' ? 'selected' : '' }}>Rent</option>
                <option value="Utilities" {{ old('category') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                <option value="Salaries" {{ old('category') == 'Salaries' ? 'selected' : '' }}>Salaries</option>
                <option value="Transport" {{ old('category') == 'Transport' ? 'selected' : '' }}>Transport</option>
                <option value="Supplies" {{ old('category') == 'Supplies' ? 'selected' : '' }}>Supplies</option>
                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount (KSh)</label>
            <input type="number" name="amount" class="form-control" step="0.01" required value="{{ old('amount') }}">
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Expense</button>
    </form>
</div>
@endsection
