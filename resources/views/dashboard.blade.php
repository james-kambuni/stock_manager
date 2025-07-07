@extends('layouts.users')

@section('title', 'Dashboard')

@section('content')
    

    <!-- Action Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('products.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 hover-shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fa fa-box fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1 text-dark">Manage Products</h6>
                            <small class="text-muted">Record sales & purchases</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('user.today.report') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 hover-shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fa fa-chart-line fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-1 text-dark">View Reports</h6>
                            <small class="text-muted">Check sales or Purchases insights</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h6>Sales Today</h6>
                    <h4>{{ $salesToday ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Purchases Today</h6>
                    <h4>{{ $purchasesToday ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
@endsection
