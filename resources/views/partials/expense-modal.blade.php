<!-- resources/views/user/partials/expense-card.blade.php -->
<div class="card shadow rounded-3 mb-4">
    <div class="card-header bg-warning bg-gradient text-dark d-flex align-items-center fw-bold">
        <i class="bi bi-cash-coin me-2"></i> New Expense
    </div>
    <div class="card-body bg-light">
        <form action="{{ route('user.expenses.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Expense Category</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pencil-square"></i></span>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Transport" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                        <input type="number" name="amount" step="0.01" class="form-control" placeholder="0.00" min="0" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Add any details..."></textarea>
            </div>

            <button type="submit" class="btn btn-sm btn-warning text-dark">
                <i class="bi bi-check2-circle me-1"></i> Submit Expense
            </button>
        </form>
    </div>
</div>
