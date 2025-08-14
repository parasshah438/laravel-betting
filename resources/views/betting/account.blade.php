@extends('layouts.app')

@section('title', 'My Account - BetMaster Pro')

@section('content')
<div class="container">
    <div class="row">
        <!-- Account Sidebar -->
        <div class="col-lg-3">
            <div class="account-sidebar">
                <div class="user-profile-card mb-3">
                    <div class="text-center">
                        <img src="https://via.placeholder.com/80" alt="Profile" class="profile-image rounded-circle mb-2">
                        <h5 class="mb-1">{{ auth()->user()->name ?? 'John Doe' }}</h5>
                        <p class="text-secondary mb-2">{{ auth()->user()->email ?? 'john.doe@example.com' }}</p>
                        <span class="badge bg-gold text-dark">VIP Member</span>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                    <a href="#wallet" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-wallet2 me-2"></i>Wallet
                    </a>
                    <a href="#transactions" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-credit-card me-2"></i>Transactions
                    </a>
                    <a href="#betting-history" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-clock-history me-2"></i>Betting History
                    </a>
                    <a href="#bonuses" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-gift me-2"></i>Bonuses
                    </a>
                    <a href="#settings" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-shield-lock me-2"></i>Security
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-bell me-2"></i>Notifications
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Content -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Profile Information</h2>
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" value="{{ auth()->user()->first_name ?? 'John' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" value="{{ auth()->user()->last_name ?? 'Doe' }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" value="{{ auth()->user()->email ?? 'john.doe@example.com' }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" value="{{ auth()->user()->phone ?? '+1 234 567 8900' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control" value="{{ auth()->user()->date_of_birth ?? '1990-01-01' }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" value="{{ auth()->user()->address ?? '123 Main Street, City, State 12345' }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2 me-1"></i>Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Account Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="stat-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-secondary">Member Since</span>
                                            <span class="fw-bold">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M Y') : 'Jan 2023' }}</span>
                                        </div>
                                    </div>
                                    <div class="stat-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-secondary">Total Bets</span>
                                            <span class="fw-bold">1,247</span>
                                        </div>
                                    </div>
                                    <div class="stat-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-secondary">Win Rate</span>
                                            <span class="fw-bold text-success">68.4%</span>
                                        </div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-secondary">VIP Level</span>
                                            <span class="badge bg-gold text-dark">Gold</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wallet Tab -->
                <div class="tab-pane fade" id="wallet">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Wallet Management</h2>
                        <div>
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#depositModal">
                                <i class="bi bi-plus-circle me-1"></i>Deposit
                            </button>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                <i class="bi bi-dash-circle me-1"></i>Withdraw
                            </button>
                        </div>
                    </div>

                    <!-- Wallet Overview -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="text-success">Main Balance</h5>
                                    <h2 class="text-success">${{ number_format(auth()->user()->balance ?? 1250.75, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="text-warning">Bonus Balance</h5>
                                    <h2 class="text-warning">${{ number_format(auth()->user()->bonus_balance ?? 125.50, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="text-info">Pending Withdrawals</h5>
                                    <h2 class="text-info">$0.00</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-plus-circle text-success me-2"></i>Deposit Funds
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Payment Method</label>
                                            <select class="form-select">
                                                <option>Credit/Debit Card</option>
                                                <option>PayPal</option>
                                                <option>Bank Transfer</option>
                                                <option>Cryptocurrency</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" placeholder="0.00" min="10">
                                            </div>
                                            <div class="form-text">Minimum deposit: $10.00</div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">$25</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm">$50</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm">$100</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm">$500</button>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-credit-card me-1"></i>Deposit Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-dash-circle text-danger me-2"></i>Withdraw Funds
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Withdrawal Method</label>
                                            <select class="form-select">
                                                <option>Bank Transfer</option>
                                                <option>PayPal</option>
                                                <option>Check</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" placeholder="0.00" min="20" max="{{ auth()->user()->balance ?? 1250.75 }}">
                                            </div>
                                            <div class="form-text">Available: ${{ number_format(auth()->user()->balance ?? 1250.75, 2) }}</div>
                                        </div>
                                        <div class="alert alert-info">
                                            <small><i class="bi bi-info-circle me-1"></i>Withdrawals are processed within 24-48 hours</small>
                                        </div>
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-send me-1"></i>Request Withdrawal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Tab -->
                <div class="tab-pane fade" id="transactions">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Transaction History</h2>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary active">All</button>
                            <button class="btn btn-outline-secondary">Deposits</button>
                            <button class="btn btn-outline-secondary">Withdrawals</button>
                            <button class="btn btn-outline-secondary">Bets</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ now()->format('M d, Y') }}</td>
                                            <td><span class="badge bg-success">Deposit</span></td>
                                            <td>Credit Card Deposit</td>
                                            <td class="text-success">+$100.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subDays(1)->format('M d, Y') }}</td>
                                            <td><span class="badge bg-primary">Bet Win</span></td>
                                            <td>Chelsea vs Arsenal - Arsenal Win</td>
                                            <td class="text-success">+$85.50</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subDays(1)->format('M d, Y') }}</td>
                                            <td><span class="badge bg-secondary">Bet</span></td>
                                            <td>Chelsea vs Arsenal - Arsenal Win</td>
                                            <td class="text-danger">-$50.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subDays(2)->format('M d, Y') }}</td>
                                            <td><span class="badge bg-warning">Bonus</span></td>
                                            <td>Welcome Bonus</td>
                                            <td class="text-warning">+$25.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subDays(3)->format('M d, Y') }}</td>
                                            <td><span class="badge bg-danger">Withdrawal</span></td>
                                            <td>Bank Transfer</td>
                                            <td class="text-danger">-$200.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <nav>
                                <ul class="pagination justify-content-center">
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
                                        <a class="page-link" href="#">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Betting History Tab -->
                <div class="tab-pane fade" id="betting-history">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Betting History</h2>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary active">All</button>
                            <button class="btn btn-outline-secondary">Pending</button>
                            <button class="btn btn-outline-secondary">Won</button>
                            <button class="btn btn-outline-secondary">Lost</button>
                        </div>
                    </div>

                    <!-- Betting Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Total Bets</h5>
                                    <h3 class="text-primary">1,247</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Won</h5>
                                    <h3 class="text-success">853</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Lost</h5>
                                    <h3 class="text-danger">394</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Win Rate</h5>
                                    <h3 class="text-warning">68.4%</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bets -->
                    <div class="card">
                        <div class="card-body">
                            <div class="bet-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Chelsea vs Arsenal</h6>
                                        <p class="text-secondary mb-1">Premier League • Arsenal Win @ 1.70</p>
                                        <small class="text-muted">{{ now()->subHours(2)->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-success">WON</span>
                                        </div>
                                        <div class="fw-bold text-success">+$85.50</div>
                                        <small class="text-muted">Stake: $50.00</small>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Lakers vs Celtics</h6>
                                        <p class="text-secondary mb-1">NBA • Over 185.5 @ 1.90</p>
                                        <small class="text-muted">{{ now()->subHours(5)->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-warning">PENDING</span>
                                        </div>
                                        <div class="fw-bold">$95.00</div>
                                        <small class="text-muted">Stake: $50.00</small>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Manchester United vs Liverpool</h6>
                                        <p class="text-secondary mb-1">Premier League • Manchester United Win @ 2.30</p>
                                        <small class="text-muted">{{ now()->subDay()->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-danger">LOST</span>
                                        </div>
                                        <div class="fw-bold text-danger">-$75.00</div>
                                        <small class="text-muted">Stake: $75.00</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bonuses Tab -->
                <div class="tab-pane fade" id="bonuses">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Bonuses & Promotions</h2>
                        <span class="badge bg-warning">2 Active Bonuses</span>
                    </div>

                    <!-- Active Bonuses -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">Welcome Bonus</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Bonus Amount:</span>
                                        <strong>$125.50</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Wagering Required:</span>
                                        <strong>$2,510.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Progress:</span>
                                        <strong>65%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: 65%"></div>
                                    </div>
                                    <small class="text-muted mt-2">Expires in 15 days</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Weekend Reload</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Bonus Amount:</span>
                                        <strong>$50.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Wagering Required:</span>
                                        <strong>$1,000.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Progress:</span>
                                        <strong>30%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: 30%"></div>
                                    </div>
                                    <small class="text-muted mt-2">Expires in 3 days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Promotions -->
                    <h4 class="mb-3">Available Promotions</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-gift text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Refer a Friend</h6>
                                    <p class="text-muted">Get $25 for each friend you refer</p>
                                    <button class="btn btn-outline-success">Learn More</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-trophy text-warning" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">VIP Rewards</h6>
                                    <p class="text-muted">Exclusive bonuses for VIP members</p>
                                    <button class="btn btn-outline-warning">View Rewards</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-lightning text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2">Daily Boost</h6>
                                    <p class="text-muted">Daily odds boosts on selected matches</p>
                                    <button class="btn btn-outline-primary">Claim Today</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings">
                    <h2 class="mb-4">Account Settings</h2>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Betting Preferences</h5>
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Default Stake Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" value="25.00">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Odds Format</label>
                                            <select class="form-select">
                                                <option>Decimal (1.50)</option>
                                                <option>Fractional (1/2)</option>
                                                <option>American (+150)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Default Market</label>
                                            <select class="form-select">
                                                <option>Match Winner</option>
                                                <option>Over/Under</option>
                                                <option>Both Teams to Score</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="acceptOddsChanges">
                                                <label class="form-check-label" for="acceptOddsChanges">
                                                    Accept odds changes automatically
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Settings</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Responsible Gambling</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Daily Deposit Limit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="500.00">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Session Time Limit</label>
                                        <select class="form-select">
                                            <option>No Limit</option>
                                            <option>1 Hour</option>
                                            <option>2 Hours</option>
                                            <option>4 Hours</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-outline-warning w-100 mb-2">Self Exclusion</button>
                                    <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#liveChatModal">Get Help</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security">
                    <h2 class="mb-4">Security Settings</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Change Password</h6>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Two-Factor Authentication</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-1">SMS Authentication</h6>
                                            <small class="text-secondary">Secure your account with SMS codes</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="smsAuth">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-1">App Authentication</h6>
                                            <small class="text-secondary">Use Google Authenticator or similar</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="appAuth" checked>
                                        </div>
                                    </div>
                                    <div class="alert alert-success">
                                        <i class="bi bi-shield-check me-1"></i>
                                        2FA is currently enabled
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Login History -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Login Activity</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Location</th>
                                            <th>Device</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ now()->format('M d, Y H:i') }}</td>
                                            <td>New York, US</td>
                                            <td>Chrome on Windows</td>
                                            <td><span class="badge bg-success">Current</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subHours(5)->format('M d, Y H:i') }}</td>
                                            <td>New York, US</td>
                                            <td>Mobile Safari</td>
                                            <td><span class="badge bg-secondary">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subDay()->format('M d, Y H:i') }}</td>
                                            <td>Chicago, US</td>
                                            <td>Firefox on Mac</td>
                                            <td><span class="badge bg-secondary">Success</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications">
                    <h2 class="mb-4">Notification Preferences</h2>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Email Notifications</h5>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="emailBetResults" checked>
                                        <label class="form-check-label" for="emailBetResults">
                                            Bet results
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="emailPromotions" checked>
                                        <label class="form-check-label" for="emailPromotions">
                                            Promotions and bonuses
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="emailDeposits">
                                        <label class="form-check-label" for="emailDeposits">
                                            Deposits and withdrawals
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="emailSecurity" checked>
                                        <label class="form-check-label" for="emailSecurity">
                                            Security alerts
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Push Notifications</h5>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="pushLive" checked>
                                        <label class="form-check-label" for="pushLive">
                                            Live betting opportunities
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="pushResults" checked>
                                        <label class="form-check-label" for="pushResults">
                                            Bet results
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="pushOdds">
                                        <label class="form-check-label" for="pushOdds">
                                            Odds changes
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="pushPromotions">
                                        <label class="form-check-label" for="pushPromotions">
                                            Special promotions
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary">Save Preferences</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
