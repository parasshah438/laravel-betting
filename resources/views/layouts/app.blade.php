<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'BetMaster Pro - Premier Online Betting Platform')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('frontend/css/betting-style.css') }}" rel="stylesheet">
    <!-- Modal CSS -->
    <link href="{{ asset('frontend/css/modals.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-trophy-fill"></i> BetMaster Pro
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('live-betting') ? 'active' : '' }}" href="{{ route('live-betting') }}">
                            <i class="bi bi-broadcast"></i> Live
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-list-ul"></i> Sports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('sports.football') }}">Football</a></li>
                            <li><a class="dropdown-item" href="{{ route('sports.basketball') }}">Basketball</a></li>
                            <li><a class="dropdown-item" href="{{ route('sports.tennis') }}">Tennis</a></li>
                            <li><a class="dropdown-item" href="{{ route('sports.esports') }}">Esports</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('promotions') ? 'active' : '' }}" href="{{ route('promotions') }}">
                            <i class="bi bi-gift"></i> Promotions
                        </a>
                    </li>
                </ul>

                <!-- User Actions -->
                <div class="d-flex align-items-center">
                    @auth
                        <!-- Balance Display -->
                        <div class="balance-display me-3">
                            <i class="bi bi-wallet2"></i> ${{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                        </div>
                        
                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('account.dashboard') }}">My Account</a></li>
                                <li><a class="dropdown-item" href="{{ route('account.bet-history') }}">Bet History</a></li>
                                <li><a class="dropdown-item" href="{{ route('account.transactions') }}">Transactions</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#depositModal">
                                    <i class="bi bi-wallet2"></i> Deposit
                                </a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                    <i class="bi bi-cash-stack"></i> Withdraw
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Guest User Actions -->
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-person-plus"></i> Sign Up
                            </button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 76px;">
        @yield('content')
    </main>

    <!-- Include Modals -->
    @include('components.auth-modals')
    @include('components.betting-modals')
    
    <!-- Include Floating Actions -->
    @include('components.floating-actions')

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="notification success show" style="position: fixed; top: 90px; right: 1rem; z-index: 9999;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="notification error show" style="position: fixed; top: 90px; right: 1rem; z-index: 9999;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="notification error show" style="position: fixed; top: 90px; right: 1rem; z-index: 9999;">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('frontend/js/betting-main.js') }}"></script>
    <!-- Modal JS -->
    <script src="{{ asset('frontend/js/modals.js') }}"></script>
    
    @stack('scripts')

    <!-- Auto-hide notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, 4000);
            });
        });
    </script>
</body>
</html>
