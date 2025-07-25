<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Panel - @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background: #f4f6f9;
        padding-top: 70px; /* space for fixed navbar */
    }

    .navbar {
        background-color: #212529 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .container {
        flex: 1;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    footer {
        background: #343a40;
        color: #ffffff;
        padding: 15px 0;
        text-align: center;
    }

    footer small {
        opacity: 0.8;
    }

    .nav-link {
        transition: 0.3s ease;
    }

    .nav-link:hover {
        color: #f8f9fa !important;
    }

    .bg-gradient-custom {
        background: linear-gradient(135deg, #1f1f2e, #343a40);
    }

    .navbar .nav-link {
        transition: all 0.3s ease-in-out;
        color: #f8f9fa !important;
    }

    .navbar .nav-link:hover {
        color: #0dcaf0 !important;
        text-decoration: underline;
    }

    .navbar .nav-link.active {
        color: #0dcaf0 !important;
        font-weight: bold;
    }

    .navbar-brand i {
        font-size: 1.3rem;
    }

    .navbar .nav-link i {
        font-size: 1.1rem;
    }
    .welcome-box {
    background: #f0f8ff;
    position: relative;
    overflow: hidden;
}

.welcome-circle {
    position: absolute;
    top: -20px;
    right: -20px;
    width: 100px;
    height: 100px;
    background: rgba(13, 110, 253, 0.15); /* Bootstrap primary with transparency */
    border-radius: 50%;
    z-index: 1;
}

/* Entry animation */
.animate-pop {
    animation: popIn 0.6s ease-out;
}

@keyframes popIn {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}


</style>


</head>
<body>

    <!-- Navbar -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
            <i class="bi bi-speedometer2 me-2"></i> User Panel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active fw-bold text-info' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center {{ request()->routeIs('profile.edit') ? 'active fw-bold text-info' : '' }}" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person-circle me-2"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn nav-link btn-link d-flex align-items-center text-white">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Main Content -->
    <div class="container mb-4">
    @if (request()->routeIs('dashboard'))
        <div id="welcomeCard" class="pb-3 border-bottom">
            <!-- Stylish Welcome Message -->
            <div class="welcome-box mb-4 p-4 rounded shadow-sm bg-light position-relative overflow-hidden animate-pop">
                <div class="position-relative z-2">
                    <h3 class="fw-bold text-dark">
                        👋 Hello, <span class="text-primary">{{ auth()->user()->name }}</span>
                    </h3>
                    <p class="text-muted mb-0">Welcome back! Here’s a quick snapshot of your activities and progress.</p>
                </div>
                <!-- Decorative circle -->
                <div class="welcome-circle"></div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        @yield('content')
    </div>
</div>


    <!-- Footer -->
    <footer>
        <div class="container">
            <small>&copy; {{ date('Y') }} J-solution Stock System. All rights reserved.</small>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const card = document.getElementById("welcomeCard");

        if (card) {
            // Animate the card in
            card.classList.add("animate__animated", "animate__fadeInDown");

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                card.classList.replace("animate__fadeInDown", "animate__fadeOutUp");

                // Optional smooth opacity fade (if needed)
                card.style.transition = "opacity 0.5s ease";
                card.style.opacity = "0.2";

                setTimeout(() => {
                    card.remove();
                }, 1000); // Wait for fade-out animation
            }, 5000); // Show for 5 seconds
        }
    });
</script>


@stack('scripts')
</body>
</html>
