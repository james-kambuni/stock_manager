<div class="card shadow rounded-3 mb-4">
    <div class="card-header bg-gradient bg-secondary text-white d-flex align-items-center">
        <i class="bi bi-bar-chart-line me-2"></i> <strong>Generate Report</strong>
    </div>
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('user.reports.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Report Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-file-earmark-bar-graph"></i></span>
                        <select class="form-select" name="type" required>
                            <option value="sales" {{ request('type') == 'sales' ? 'selected' : '' }}>Sales Report</option>
                            <option value="purchases" {{ request('type') == 'purchases' ? 'selected' : '' }}>Purchases Report</option>
                            <option value="inventory" {{ request('type') == 'inventory' ? 'selected' : '' }}>Inventory Report</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                        <input type="date" class="form-control" name="start" value="{{ request('start') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                        <input type="date" class="form-control" name="end" value="{{ request('end') }}" required>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-play-circle"></i> Generate
                    </button>
                </div>
            </div>
        </form>

        {{-- Report Results --}}
        <div id="reportResults" class="mt-4 p-3 bg-white border rounded small">
            @if(isset($reportType) && isset($reportData))
                <h6 class="fw-bold text-dark mb-3">
                    Showing {{ ucfirst($reportType) }} Report from {{ request('start') }} to {{ request('end') }}
                </h6>

                @if(count($reportData) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="table-secondary">
                                <tr>
                                    @foreach(array_keys($reportData[0]->toArray()) as $header)
                                        <th>{{ ucwords(str_replace('_', ' ', $header)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $row)
                                    <tr>
                                        @foreach($row->toArray() as $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-circle"></i> No {{ $reportType }} found in selected date range.
                    </div>
                @endif
            @else
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Select report type and date range to view data.
                </div>
            @endif
        </div>
    </div>
</div>
