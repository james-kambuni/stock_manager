@extends('layouts.users')

@section('title', 'Dashboard')

@section('content')
<style>
    .action-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        color: white;
    }

    .action-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
    }

    .action-icon {
        width: 50px;
        height: 50px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 15px;
        transition: background-color 0.3s ease;
    }

    .action-card:hover .action-icon {
        background-color: rgba(255, 255, 255, 0.3);
    }

    .action-title {
        font-weight: 600;
        color: white;
    }

    .action-description {
        font-size: 0.875rem;
        color: #f8f9fa;
    }

    .bg-gradient-products {
        background: linear-gradient(135deg, #0d6efd, #6f42c1);
    }

    .bg-gradient-reports {
        background: linear-gradient(135deg, #198754, #20c997);
    }
    .arrow-icon i,
.arrow-icon small {
    transition: color 0.3s ease, opacity 0.3s ease;
}

a:hover .arrow-icon i,
a:hover .arrow-icon small {
    color: #ffc107 !important;
    opacity: 1;
}

.view-button {
    display: inline-block;
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: background-color 0.3s ease, transform 0.3s ease;
    cursor: pointer;
}

.view-button:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}


/* Pencil Animation */
.writing-pencil {
    animation: pencil-dance 1.5s ease-in-out infinite;
    position: relative;
    z-index: 2;
}

/* Animated SVG Line */
.drawing-line {
    stroke: white;
    stroke-width: 2;
    stroke-dasharray: 400;
    stroke-dashoffset: 400;
    animation: draw-line 10s linear infinite;
    position: relative;
    z-index: 1;
}

/* Line Animation */
@keyframes draw-line {
    0% {
        stroke-dashoffset: 400;
    }
    100% {
        stroke-dashoffset: 0;
    }
}

/* Pencil Dance Animation - no movement, just rotation and bounce */
@keyframes pencil-dance {
    0% { transform: rotate(0deg) translateY(0); }
    20% { transform: rotate(-10deg) translateY(-1px); }
    40% { transform: rotate(10deg) translateY(1px); }
    60% { transform: rotate(-8deg) translateY(-1px); }
    80% { transform: rotate(8deg) translateY(1px); }
    100% { transform: rotate(0deg) translateY(0); }
}




</style>

<!-- Action Cards -->
<!-- Action Cards -->
<div class="row g-4 mb-4 justify-content-center position-relative">
    <!-- Animated Drawing Line (at the very top) -->
    

    <!-- line -->
        <!-- <svg width="100%" height="20" viewBox="0 0 100 20" preserveAspectRatio="none" class="drawing-line">
            <line x1="0" y1="10" x2="100" y2="10" />
        </svg> -->
    <!-- Products Card -->
<div class="col-12 col-sm-8 col-md-6 col-lg-4">
    <a href="{{ route('user.products.index') }}" class="text-decoration-none">
        <div class="card action-card shadow-sm border-0 h-100 bg-gradient-products text-white position-relative">
            <div class="card-body d-flex align-items-start">
                <div class="action-icon">
                    <i class="fa fa-box"></i>
                </div>
                <div>
                    <h6 class="action-title mb-1">Manage stock</h6>
                    <p class="action-description mb-0">Record sales, purchases, and expenses.</p>
                </div>
            </div>

            <!-- Pencil and View Button -->
            <div class="text-center mb-3">
                <i class="fas fa-pencil-alt fa-lg text-white writing-pencil mb-2"></i>
                <div class="view-button mt-1">Click to begin</div>
            </div>
        </div>
    </a>
</div>

<!-- Reports Card -->
<div class="col-12 col-sm-8 col-md-6 col-lg-4">
    <a href="{{ route('user.today.report') }}" class="text-decoration-none">
        <div class="card action-card shadow-sm border-0 h-100 bg-gradient-reports text-white position-relative">
            <div class="card-body d-flex align-items-start">
                <div class="action-icon">
                    <i class="fa fa-chart-line"></i>
                </div>
                <div>
                    <h6 class="action-title mb-1">View Today’s Reports</h6>
                    <p class="action-description mb-0">Get a quick overview of your daily sales, purchases, and expenses.</p>
                </div>
            </div>

            <!-- Arrow and View Button -->
            <div class="text-center mb-3">
                <i class="fas fa-arrow-down fa-lg text-white opacity-75 mb-2"></i>
                <div class="view-button mt-1">View report</div>
            </div>
        </div>
    </a>
</div>


</div>
<!-- Combined Chart -->
<div class="row justify-content-center mb-4">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h6 class="mb-0 fw-bold">Today’s Sales & Purchases Overview</h6>
            </div>
            <div class="card-body bg-light">
                <canvas id="todayChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(90deg, #0d6efd, #198754);
    }

    #todayChart {
        max-height: 300px;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('todayChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Today'],
            datasets: [
                
               
                {
                    label: 'Sales',
                    type: 'bar',
                    data: [{{ $salesToday ?? 0 }}],
                    backgroundColor: 'rgba(13, 110, 253, 0.5)',
                    borderRadius: 15,
                    barThickness: 40
                },
                {
                    label: 'Purchases',
                    type: 'bar',
                    data: [{{ $purchasesToday ?? 0 }}],
                    backgroundColor: 'rgba(255, 193, 7, 0.5)',
                    borderRadius: 15,
                    barThickness: 40
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#f8f9fa',
                    titleColor: '#000',
                    bodyColor: '#000',
                    borderColor: '#dee2e6',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.formattedValue}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#495057',
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: '#e9ecef',
                        borderDash: [5, 5]
                    }
                },
                x: {
                    ticks: {
                        color: '#495057',
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush

@endsection
