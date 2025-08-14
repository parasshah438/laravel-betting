@extends('layouts.app')

@section('title', 'Betting History - BetMaster Pro')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">Betting History</h1>
            <p class="text-secondary mb-0">Track all your bets and analyze your performance</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-bar-chart me-1"></i>View Stats
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-graph-up text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 text-primary">1,247</h3>
                    <p class="mb-0 text-secondary">Total Bets</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-trophy text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 text-success">853</h3>
                    <p class="mb-0 text-secondary">Bets Won</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 text-danger">394</h3>
                    <p class="mb-0 text-secondary">Bets Lost</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-percent text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 text-warning">68.4%</h3>
                    <p class="mb-0 text-secondary">Win Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit/Loss Chart -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profit/Loss Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="profitLossChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Performance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Total Wagered:</span>
                        <strong>$12,567.50</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Total Returns:</span>
                        <strong class="text-success">$13,842.25</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Net Profit:</span>
                        <strong class="text-success">+$1,274.75</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">ROI:</span>
                        <strong class="text-success">+10.15%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Avg. Stake:</span>
                        <strong>$42.50</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Biggest Win:</span>
                        <strong class="text-success">$587.50</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <select class="form-select">
                        <option selected>Last 30 Days</option>
                        <option>Last 7 Days</option>
                        <option>Last 3 Months</option>
                        <option>Last Year</option>
                        <option>All Time</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option selected>All Bets</option>
                        <option>Pending</option>
                        <option>Won</option>
                        <option>Lost</option>
                        <option>Voided</option>
                        <option>Cashed Out</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sport</label>
                    <select class="form-select">
                        <option selected>All Sports</option>
                        <option>Football</option>
                        <option>Basketball</option>
                        <option>Tennis</option>
                        <option>Baseball</option>
                        <option>Soccer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bet Type</label>
                    <select class="form-select">
                        <option selected>All Types</option>
                        <option>Single</option>
                        <option>Multiple</option>
                        <option>System</option>
                        <option>Live</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search teams, leagues...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Betting History Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Bet History</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active">All</button>
                <button class="btn btn-outline-secondary">Singles</button>
                <button class="btn btn-outline-secondary">Multiples</button>
                <button class="btn btn-outline-secondary">Live</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th>Selection</th>
                            <th>Odds</th>
                            <th>Stake</th>
                            <th>Potential Return</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Bet 1 - Won -->
                        <tr>
                            <td>
                                <div>{{ now()->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Chelsea vs Arsenal</div>
                                <small class="text-muted">Premier League</small>
                            </td>
                            <td>
                                <div>Arsenal Win</div>
                                <small class="text-muted">Match Winner</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">1.70</span>
                            </td>
                            <td>$50.00</td>
                            <td>$85.50</td>
                            <td>
                                <span class="badge bg-success">WON</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Share">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 2 - Pending -->
                        <tr>
                            <td>
                                <div>{{ now()->subHours(2)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subHours(2)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Lakers vs Celtics</div>
                                <small class="text-muted">NBA</small>
                            </td>
                            <td>
                                <div>Over 185.5 Points</div>
                                <small class="text-muted">Total Points</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">1.90</span>
                            </td>
                            <td>$50.00</td>
                            <td>$95.00</td>
                            <td>
                                <span class="badge bg-warning">PENDING</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning" title="Cash Out">
                                        <i class="bi bi-cash-coin"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 3 - Lost -->
                        <tr>
                            <td>
                                <div>{{ now()->subDay()->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subDay()->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Man United vs Liverpool</div>
                                <small class="text-muted">Premier League</small>
                            </td>
                            <td>
                                <div>Man United Win</div>
                                <small class="text-muted">Match Winner</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">2.30</span>
                            </td>
                            <td>$75.00</td>
                            <td>$172.50</td>
                            <td>
                                <span class="badge bg-danger">LOST</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Bet Again">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 4 - Multiple Bet Won -->
                        <tr>
                            <td>
                                <div>{{ now()->subDays(2)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subDays(2)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Multiple Bet (3 selections)</div>
                                <small class="text-muted">
                                    <i class="bi bi-list-ul me-1"></i>View selections
                                </small>
                            </td>
                            <td>
                                <div>Barcelona Win + Real Madrid Win + Over 2.5</div>
                                <small class="text-muted">Treble</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">4.85</span>
                            </td>
                            <td>$25.00</td>
                            <td>$121.25</td>
                            <td>
                                <span class="badge bg-success">WON</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Repeat Bet">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 5 - Tennis Lost -->
                        <tr>
                            <td>
                                <div>{{ now()->subDays(3)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subDays(3)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Djokovic vs Nadal</div>
                                <small class="text-muted">French Open</small>
                            </td>
                            <td>
                                <div>Djokovic Win</div>
                                <small class="text-muted">Match Winner</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">1.65</span>
                            </td>
                            <td>$100.00</td>
                            <td>$165.00</td>
                            <td>
                                <span class="badge bg-danger">LOST</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 6 - Basketball Won -->
                        <tr>
                            <td>
                                <div>{{ now()->subDays(4)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subDays(4)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Warriors vs Nets</div>
                                <small class="text-muted">NBA</small>
                            </td>
                            <td>
                                <div>Warriors -5.5</div>
                                <small class="text-muted">Point Spread</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">1.95</span>
                            </td>
                            <td>$60.00</td>
                            <td>$117.00</td>
                            <td>
                                <span class="badge bg-success">WON</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Share">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 7 - Cashed Out -->
                        <tr>
                            <td>
                                <div>{{ now()->subDays(5)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subDays(5)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">Real Madrid vs Barcelona</div>
                                <small class="text-muted">El Clasico</small>
                            </td>
                            <td>
                                <div>Real Madrid Win</div>
                                <small class="text-muted">Match Winner</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">2.10</span>
                            </td>
                            <td>$80.00</td>
                            <td>$168.00</td>
                            <td>
                                <span class="badge bg-info">CASHED OUT</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bet 8 - System Bet -->
                        <tr>
                            <td>
                                <div>{{ now()->subWeek()->format('M d, Y') }}</div>
                                <small class="text-muted">{{ now()->subWeek()->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">System Bet 2/4</div>
                                <small class="text-muted">
                                    <i class="bi bi-diagram-3 me-1"></i>4 selections
                                </small>
                            </td>
                            <td>
                                <div>2 out of 4 selections</div>
                                <small class="text-muted">System 2/4</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">Various</span>
                            </td>
                            <td>$30.00</td>
                            <td>$45.75</td>
                            <td>
                                <span class="badge bg-success">PARTIAL WIN</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <nav aria-label="Betting history pagination">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">25</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
            <div class="text-center mt-2">
                <small class="text-muted">Showing 1-10 of 1,247 bets</small>
            </div>
        </div>
    </div>
</div>

<!-- Bet Details Modal -->
<div class="modal fade" id="betDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bet Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="mb-3">Event Information</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Event:</span>
                            <strong>Chelsea vs Arsenal</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">League:</span>
                            <strong>Premier League</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Date:</span>
                            <strong>{{ now()->format('M d, Y H:i') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Selection:</span>
                            <strong>Arsenal Win @ 1.70</strong>
                        </div>
                        
                        <h6 class="mb-3">Bet Information</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Bet Type:</span>
                            <strong>Single Bet</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Stake:</span>
                            <strong>$50.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Potential Return:</span>
                            <strong>$85.50</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Actual Return:</span>
                            <strong class="text-success">$85.50</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Profit:</span>
                            <strong class="text-success">+$35.50</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header text-center">
                                <span class="badge bg-success fs-6">WON</span>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title">Final Score</h5>
                                <h2 class="text-primary">1 - 2</h2>
                                <p class="card-text">
                                    <small class="text-muted">Full Time</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>Bet Again
                </button>
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-share me-1"></i>Share
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Chart.js configuration for profit/loss chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('profitLossChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Profit/Loss',
                    data: [120, 190, 150, 280, 220, 350, 180, 420, 380, 450, 320, 580],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
});

// Event listeners for bet details modal
document.addEventListener('click', function(e) {
    if (e.target.closest('[title="View Details"]')) {
        const modal = new bootstrap.Modal(document.getElementById('betDetailsModal'));
        modal.show();
    }
});
</script>
@endsection
