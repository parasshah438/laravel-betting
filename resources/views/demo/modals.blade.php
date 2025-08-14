@extends('layouts.app')

@section('title', 'Modal Demo - BetMaster Pro')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 text-primary mb-3">Modal System Demo</h1>
                <p class="lead">Test all our beautiful Bootstrap 5 modal popups</p>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Authentication Modals -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-circle me-2"></i>Authentication
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-text">User login, registration, and password recovery modals.</p>
                    <div class="mt-auto">
                        <button class="btn btn-outline-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login Modal
                        </button>
                        <button class="btn btn-outline-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-person-plus me-1"></i> Register Modal
                        </button>
                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                            <i class="bi bi-key me-1"></i> Forgot Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Betting Modals -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-ticket-perforated me-2"></i>Betting
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-text">Deposit, withdraw, and bet slip management modals.</p>
                    <div class="mt-auto">
                        <button class="btn btn-outline-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#depositModal">
                            <i class="bi bi-wallet2 me-1"></i> Deposit Modal
                        </button>
                        <button class="btn btn-outline-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="bi bi-cash-stack me-1"></i> Withdraw Modal
                        </button>
                        <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#betSlipModal">
                            <i class="bi bi-ticket-perforated me-1"></i> Bet Slip
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Communication Modals -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Communication
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-text">Contact forms, live chat, and notification modals.</p>
                    <div class="mt-auto">
                        <button class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-envelope me-1"></i> Contact Us
                        </button>
                        <button class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#liveChatModal">
                            <i class="bi bi-chat-dots me-1"></i> Live Chat
                        </button>
                        <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                            <i class="bi bi-bell me-1"></i> Notifications
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Features Demo -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-star me-2"></i>Modal Features
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <i class="bi bi-palette text-primary" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Beautiful Design</h6>
                                <p class="text-muted small">Professional Bootstrap 5 modals with gradients and animations</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <i class="bi bi-phone text-success" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Fully Responsive</h6>
                                <p class="text-muted small">Perfect display on desktop, tablet, and mobile devices</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <i class="bi bi-check2-circle text-warning" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Real-time Validation</h6>
                                <p class="text-muted small">Client-side validation with instant feedback</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <i class="bi bi-lightning text-danger" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Interactive Elements</h6>
                                <p class="text-muted small">Password strength, chat functionality, and more</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Quick Test Actions</h5>
                    <div class="btn-group flex-wrap" role="group">
                        <button class="btn btn-primary" onclick="BettingModals.showLoginModal()">Show Login</button>
                        <button class="btn btn-primary" onclick="BettingModals.showRegisterModal()">Show Register</button>
                        <button class="btn btn-success" onclick="BettingModals.showDepositModal()">Show Deposit</button>
                        <button class="btn btn-warning" onclick="BettingModals.showWithdrawModal()">Show Withdraw</button>
                        <button class="btn btn-info" onclick="BettingModals.showContactModal()">Show Contact</button>
                        <button class="btn btn-secondary" onclick="BettingModals.showLiveChatModal()">Show Live Chat</button>
                    </div>
                    <p class="text-muted mt-3 mb-0">Notice the floating action button in the bottom right corner!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sample Bet Data for Bet Slip Demo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some sample selections to demonstrate bet slip
    if (typeof betSlipSelections !== 'undefined') {
        // Pre-populate bet slip for demo
        setTimeout(() => {
            if (betSlipSelections.length === 0) {
                betSlipSelections.push({
                    selection: 'Manchester United Win',
                    odds: 2.85,
                    match: 'Manchester United vs Liverpool',
                    stake: 0
                });
                betSlipSelections.push({
                    selection: 'Over 2.5 Goals',
                    odds: 1.65,
                    match: 'Manchester United vs Liverpool',
                    stake: 0
                });
                if (typeof updateBetSlip === 'function') {
                    updateBetSlip();
                }
            }
        }, 1000);
    }
});
</script>

@endsection
