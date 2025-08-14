@extends('layouts.app')

@section('title', 'Responsive Modal Test - BetMaster Pro')

@section('content')
<div class="container-fluid py-4">
    <!-- Responsive Test Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h4><i class="bi bi-phone me-2"></i>Responsive Modal System Test</h4>
                <p class="mb-2">This page demonstrates how all modals adapt perfectly to different screen sizes:</p>
                <ul class="mb-0">
                    <li><strong>Desktop (>768px):</strong> Full-width modals with side-by-side layouts</li>
                    <li><strong>Tablet (576px - 768px):</strong> Adjusted spacing and font sizes</li>
                    <li><strong>Mobile (<576px):</strong> Full-screen modals with stacked elements</li>
                    <li><strong>Very Small (<480px):</strong> Floating button hidden, optimized layouts</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Screen Size Indicator -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Current Screen Size</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded d-block d-sm-none">
                                <i class="bi bi-phone text-danger" style="font-size: 2rem;"></i>
                                <h6 class="text-danger mt-2">Extra Small</h6>
                                <small>&lt;576px</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded d-none d-sm-block d-md-none">
                                <i class="bi bi-tablet text-warning" style="font-size: 2rem;"></i>
                                <h6 class="text-warning mt-2">Small</h6>
                                <small>576px - 768px</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded d-none d-md-block d-lg-none">
                                <i class="bi bi-laptop text-info" style="font-size: 2rem;"></i>
                                <h6 class="text-info mt-2">Medium</h6>
                                <small>768px - 992px</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded d-none d-lg-block">
                                <i class="bi bi-display text-success" style="font-size: 2rem;"></i>
                                <h6 class="text-success mt-2">Large+</h6>
                                <small>&gt;992px</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Modal Tests -->
    <div class="row g-4">
        <!-- Authentication Modals Test -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>Authentication Modals
                    </h5>
                </div>
                <div class="card-body">
                    <p>Test responsive authentication forms:</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login Modal
                            <small class="d-block">Social buttons stack on mobile</small>
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-person-plus me-2"></i>Register Modal
                            <small class="d-block">Form fields optimize for mobile</small>
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-envelope me-2"></i>Contact Modal
                            <small class="d-block">Sidebar moves to top on mobile</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Betting Modals Test -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-wallet2 me-2"></i>Betting Modals
                    </h5>
                </div>
                <div class="card-body">
                    <p>Test responsive betting interfaces:</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#depositModal">
                            <i class="bi bi-plus-circle me-2"></i>Deposit Modal
                            <small class="d-block">Payment methods: 3→2 columns on mobile</small>
                        </button>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="bi bi-cash-stack me-2"></i>Withdraw Modal
                            <small class="d-block">Quick amounts: 3→2 columns on mobile</small>
                        </button>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#betSlipModal">
                            <i class="bi bi-ticket-perforated me-2"></i>Bet Slip Modal
                            <small class="d-block">Optimized stake inputs for mobile</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Communication Modals Test -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Communication
                    </h5>
                </div>
                <div class="card-body">
                    <p>Test responsive communication features:</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#liveChatModal">
                            <i class="bi bi-chat-dots me-2"></i>Live Chat
                            <small class="d-block">Chat messages optimize for mobile</small>
                        </button>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                            <i class="bi bi-bell me-2"></i>Notifications
                            <small class="d-block">Notification items stack properly</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Features Demo -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-phone me-2"></i>Responsive Features
                    </h5>
                </div>
                <div class="card-body">
                    <div class="responsive-feature-list">
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-success me-3"></i>
                            <div>
                                <strong>Modal Width Adaptation</strong>
                                <small class="d-block text-muted">Full width on mobile, centered on desktop</small>
                            </div>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-success me-3"></i>
                            <div>
                                <strong>Grid System Optimization</strong>
                                <small class="d-block text-muted">Payment methods, quick amounts auto-adjust</small>
                            </div>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-success me-3"></i>
                            <div>
                                <strong>Button Size Scaling</strong>
                                <small class="d-block text-muted">Larger touch targets on mobile</small>
                            </div>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-success me-3"></i>
                            <div>
                                <strong>Font Size Adaptation</strong>
                                <small class="d-block text-muted">Optimized readability on all screens</small>
                            </div>
                        </div>
                        <div class="feature-item d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3"></i>
                            <div>
                                <strong>Touch-Friendly Interface</strong>
                                <small class="d-block text-muted">Proper spacing and touch targets</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Breakpoints Demo -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-rulers me-2"></i>Responsive Breakpoints
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Screen Size</th>
                                    <th>Breakpoint</th>
                                    <th>Modal Adaptations</th>
                                    <th>Grid Changes</th>
                                    <th>Special Features</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-display text-success"></i> Large</td>
                                    <td>&gt;768px</td>
                                    <td>Centered, max-width 500px</td>
                                    <td>3-column grids maintained</td>
                                    <td>FAB with tooltips</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-tablet text-info"></i> Medium</td>
                                    <td>576px-768px</td>
                                    <td>Reduced padding, smaller titles</td>
                                    <td>2-3 column adaptive grids</td>
                                    <td>Smaller FAB</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-phone text-warning"></i> Small</td>
                                    <td>&lt;576px</td>
                                    <td>Full width with margins</td>
                                    <td>2-column grids, stacked socials</td>
                                    <td>Larger buttons</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-phone text-danger"></i> Extra Small</td>
                                    <td>&lt;480px</td>
                                    <td>Minimal margins</td>
                                    <td>Single column layouts</td>
                                    <td>FAB hidden</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Instructions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-success">
                <h5><i class="bi bi-lightbulb me-2"></i>How to Test Responsiveness:</h5>
                <ol>
                    <li><strong>Desktop:</strong> Use browser dev tools to simulate different screen sizes</li>
                    <li><strong>Chrome:</strong> F12 → Toggle device toolbar → Select device presets</li>
                    <li><strong>Firefox:</strong> F12 → Responsive Design Mode</li>
                    <li><strong>Safari:</strong> Develop → Responsive Design Mode</li>
                    <li><strong>Physical Test:</strong> Open on actual mobile/tablet devices</li>
                </ol>
                <p class="mb-0 mt-3"><strong>Try these actions:</strong> Resize browser window while modals are open, test touch interactions on mobile, rotate device orientation</p>
            </div>
        </div>
    </div>
</div>

<!-- Add some sample bet slip data for testing -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add responsive detection
    function updateScreenInfo() {
        const width = window.innerWidth;
        let screenType = '';
        
        if (width < 576) screenType = 'Extra Small (<576px)';
        else if (width < 768) screenType = 'Small (576px-768px)';
        else if (width < 992) screenType = 'Medium (768px-992px)';
        else screenType = 'Large (≥992px)';
        
        // Update screen indicator if it exists
        const indicator = document.getElementById('screenIndicator');
        if (indicator) {
            indicator.textContent = `Current: ${screenType} - ${width}px`;
        }
        
        console.log(`Screen size: ${screenType} - ${width}px`);
    }
    
    // Update on resize
    window.addEventListener('resize', updateScreenInfo);
    updateScreenInfo();
    
    // Add sample bet slip data after a delay
    setTimeout(() => {
        if (typeof betSlipSelections !== 'undefined' && betSlipSelections.length === 0) {
            betSlipSelections.push({
                selection: 'Test Selection 1',
                odds: 2.50,
                match: 'Test Match A vs B',
                stake: 0
            });
            betSlipSelections.push({
                selection: 'Test Selection 2',
                odds: 1.80,
                match: 'Test Match C vs D',
                stake: 0
            });
            
            if (typeof updateBetSlip === 'function') {
                updateBetSlip();
            }
        }
    }, 1000);
});
</script>

<style>
/* Additional responsive test styles */
.feature-item {
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: rgba(0,123,255,0.05);
    border-radius: 8px;
    padding: 0.5rem;
    margin: -0.5rem;
}

@media (max-width: 576px) {
    .card-body p {
        font-size: 0.875rem;
    }
    
    .btn small {
        display: none !important;
    }
}

@media (max-width: 480px) {
    .alert ul {
        padding-left: 1rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
}

/* Screen size indicators - only show on appropriate screens */
.responsive-indicator {
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 10px;
    text-align: center;
    font-weight: bold;
}

@media (max-width: 575.98px) {
    .show-xs { display: block !important; background: #f8d7da; color: #721c24; }
    .hide-xs { display: none !important; }
}

@media (min-width: 576px) and (max-width: 767.98px) {
    .show-sm { display: block !important; background: #fff3cd; color: #856404; }
    .hide-sm { display: none !important; }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .show-md { display: block !important; background: #d1ecf1; color: #0c5460; }
    .hide-md { display: none !important; }
}

@media (min-width: 992px) {
    .show-lg { display: block !important; background: #d4edda; color: #155724; }
    .hide-lg { display: none !important; }
}
</style>

@endsection
