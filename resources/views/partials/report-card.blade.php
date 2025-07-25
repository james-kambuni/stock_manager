<!-- resources/views/user/partials/report-card.blade.php -->
<div class="card shadow rounded-3 mb-4">
    <div class="card-header bg-gradient bg-secondary text-white d-flex align-items-center">
        <i class="bi bi-bar-chart-line me-2"></i> <strong>Generate Report</strong>
    </div>
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('user.expenses.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Report Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-file-earmark-bar-graph"></i></span>
                        <select class="form-select" name="type" required>
                            <option value="sales">Sales Report</option>
                            <option value="purchases">Purchases Report</option>
                            <option value="inventory">Inventory Report</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                        <input type="date" class="form-control" name="start" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                        <input type="date" class="form-control" name="end" required>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-play-circle"></i> Generate
                    </button>
                </div>
            </div>
        </form>

        <div id="reportResults" class="mt-4 p-3 bg-white border rounded text-muted small">
            <i class="bi bi-info-circle me-1"></i> Select report type and date range to view data.
        </div>
    </div>
</div>
