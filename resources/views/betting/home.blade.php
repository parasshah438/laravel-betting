@extends('layouts.app')

@section('title', 'BetMaster Pro - Premier Online Betting Platform')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 p-0 sidebar">
            <!-- Sports Categories -->
            <div class="sport-category active">
                <div>
                    <i class="bi bi-dribbble"></i>
                    <span class="ms-2">Football</span>
                </div>
                <span class="badge bg-primary">12</span>
            </div>
            
            <div class="sport-category">
                <div>
                    <i class="bi bi-basketball"></i>
                    <span class="ms-2">Basketball</span>
                </div>
                <span class="badge bg-secondary">8</span>
            </div>
            
            <div class="sport-category">
                <div>
                    <i class="bi bi-tennis-ball"></i>
                    <span class="ms-2">Tennis</span>
                </div>
                <span class="badge bg-secondary">15</span>
            </div>
            
            <div class="sport-category">
                <div>
                    <i class="bi bi-controller"></i>
                    <span class="ms-2">Esports</span>
                </div>
                <span class="badge bg-secondary">6</span>
            </div>
            
            <div class="sport-category">
                <div>
                    <i class="bi bi-broadcast live-indicator"></i>
                    <span class="ms-2">Live Now</span>
                </div>
                <span class="badge bg-danger">4</span>
            </div>
        </div>

        <!-- Main Betting Area -->
        <div class="col-lg-7">
            <div class="p-3">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="text-primary mb-1">Featured Matches</h2>
                        <p class="text-secondary mb-0">Today's top betting opportunities</p>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active">All</button>
                        <button type="button" class="btn btn-outline-primary">Live</button>
                        <button type="button" class="btn btn-outline-primary">Upcoming</button>
                    </div>
                </div>

                <!-- Match Cards -->
                <div class="row">
                    <!-- Live Match -->
                    <div class="col-12 mb-3">
                        <div class="match-card live-match">
                            <div class="match-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="league-name">Premier League</div>
                                        <div class="match-time">
                                            <span class="match-minute">65'</span> • Live Now
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <i class="bi bi-eye me-1"></i> 12,458 watching
                                    </div>
                                </div>
                            </div>
                            
                            <div class="teams-section">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/32" alt="Manchester United" class="team-logo me-2">
                                            <span class="team-name">Manchester United</span>
                                        </div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="match-score">2 - 1</div>
                                        <div class="vs-separator">vs</div>
                                    </div>
                                    <div class="col-5 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span class="team-name">Liverpool</span>
                                            <img src="https://via.placeholder.com/32" alt="Liverpool" class="team-logo ms-2">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="odds-container">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Man United</div>
                                            <button class="odds-btn" data-selection="Man United Win" data-odds="2.85" data-match="Manchester United vs Liverpool" onclick="addToBetSlip(this)">
                                                <div class="odds-value">2.85</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Draw</div>
                                            <button class="odds-btn" data-selection="Draw" data-odds="3.20" data-match="Manchester United vs Liverpool" onclick="addToBetSlip(this)">
                                                <div class="odds-value">3.20</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Liverpool</div>
                                            <button class="odds-btn" data-selection="Liverpool Win" data-odds="2.45" data-match="Manchester United vs Liverpool" onclick="addToBetSlip(this)">
                                                <div class="odds-value">2.45</div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="odds-label">Over 2.5 Goals</div>
                                            <button class="odds-btn" data-selection="Over 2.5" data-odds="1.65" data-match="Manchester United vs Liverpool" onclick="addToBetSlip(this)">
                                                <div class="odds-value">1.65</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="odds-label">Under 2.5 Goals</div>
                                            <button class="odds-btn" data-selection="Under 2.5" data-odds="2.10" data-match="Manchester United vs Liverpool" onclick="addToBetSlip(this)">
                                                <div class="odds-value">2.10</div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Match 1 -->
                    <div class="col-12 mb-3">
                        <div class="match-card">
                            <div class="match-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="league-name">La Liga</div>
                                        <div class="match-time">Today, 20:00</div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="teams-section">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/32" alt="Barcelona" class="team-logo me-2">
                                            <span class="team-name">Barcelona</span>
                                        </div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="vs-separator">vs</div>
                                    </div>
                                    <div class="col-5 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span class="team-name">Real Madrid</span>
                                            <img src="https://via.placeholder.com/32" alt="Real Madrid" class="team-logo ms-2">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="odds-container">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Barcelona</div>
                                            <button class="odds-btn" data-selection="Barcelona Win" data-odds="2.20" data-match="Barcelona vs Real Madrid" onclick="addToBetSlip(this)">
                                                <div class="odds-value">2.20</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Draw</div>
                                            <button class="odds-btn" data-selection="Draw" data-odds="3.40" data-match="Barcelona vs Real Madrid" onclick="addToBetSlip(this)">
                                                <div class="odds-value">3.40</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Real Madrid</div>
                                            <button class="odds-btn" data-selection="Real Madrid Win" data-odds="3.10" data-match="Barcelona vs Real Madrid" onclick="addToBetSlip(this)">
                                                <div class="odds-value">3.10</div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <button class="btn btn-outline-primary btn-sm">
                                        +15 More Markets
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Match 2 -->
                    <div class="col-12 mb-3">
                        <div class="match-card">
                            <div class="match-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="league-name">Serie A</div>
                                        <div class="match-time">Tomorrow, 18:45</div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="teams-section">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/32" alt="Juventus" class="team-logo me-2">
                                            <span class="team-name">Juventus</span>
                                        </div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="vs-separator">vs</div>
                                    </div>
                                    <div class="col-5 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span class="team-name">AC Milan</span>
                                            <img src="https://via.placeholder.com/32" alt="AC Milan" class="team-logo ms-2">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="odds-container">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Juventus</div>
                                            <button class="odds-btn" data-selection="Juventus Win" data-odds="2.70" data-match="Juventus vs AC Milan" onclick="addToBetSlip(this)">
                                                <div class="odds-value">2.70</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">Draw</div>
                                            <button class="odds-btn" data-selection="Draw" data-odds="3.00" data-match="Juventus vs AC Milan" onclick="addToBetSlip(this)">
                                                <div class="odds-value">3.00</div>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <div class="odds-label">AC Milan</div>
                                            <button class="odds-btn" data-selection="AC Milan Win" data-odds="2.90" data-match="Juventus vs AC Milan" onclick="addToBetSlip(this)">
                                                <div class="odds-value">2.90</div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bet Slip -->
        <div class="col-lg-3">
            <div class="p-3">
                <div class="bet-slip">
                    <div class="bet-slip-header">
                        <h5 class="bet-slip-title">Bet Slip</h5>
                        <span class="bet-slip-count">0</span>
                    </div>
                    
                    <div class="bet-slip-content" id="betSlipContent">
                        <div class="text-center p-4">
                            <i class="bi bi-ticket-perforated" style="font-size: 3rem; color: var(--text-secondary);"></i>
                            <p class="text-secondary mt-2 mb-0">Click on odds to add selections</p>
                        </div>
                    </div>

                    <!-- Bet Summary (Hidden initially) -->
                    <div class="bet-summary" id="betSummary" style="display: none;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Stake:</span>
                            <span class="total-stake">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Odds:</span>
                            <span class="total-odds">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Potential Return:</strong>
                            <strong class="potential-return">$0.00</strong>
                        </div>
                        <button class="place-bet-btn" id="placeBetBtn" data-bs-toggle="modal" data-bs-target="#betSlipModal" disabled>
                            Place Bet
                        </button>
                    </div>
                </div>

                <!-- Quick Bet Options -->
                <div class="mt-3">
                    <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Quick Stakes</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-4">
                                    <button class="btn btn-outline-primary btn-sm w-100 quick-stake" data-amount="10">$10</button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-primary btn-sm w-100 quick-stake" data-amount="25">$25</button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-primary btn-sm w-100 quick-stake" data-amount="50">$50</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Bet Slip Functionality
let betSlipSelections = [];

function addToBetSlip(button) {
    const selection = button.dataset.selection;
    const odds = parseFloat(button.dataset.odds);
    const match = button.dataset.match;
    
    // Check if selection already exists
    const existingIndex = betSlipSelections.findIndex(s => s.selection === selection && s.match === match);
    
    if (existingIndex > -1) {
        // Remove existing selection
        betSlipSelections.splice(existingIndex, 1);
        button.classList.remove('selected');
        showNotification('Removed', `${selection} removed from bet slip`, 'info');
    } else {
        // Add new selection
        const newSelection = {
            selection: selection,
            odds: odds,
            match: match,
            stake: 0
        };
        betSlipSelections.push(newSelection);
        button.classList.add('selected');
        showNotification('Added', `${selection} added to bet slip`, 'success');
    }
    
    updateBetSlip();
}

function updateBetSlip() {
    const betSlipContent = document.getElementById('betSlipContent');
    const betSummary = document.getElementById('betSummary');
    const placeBetBtn = document.getElementById('placeBetBtn');
    const betSlipCount = document.querySelector('.bet-slip-count');
    
    // Update bet slip count
    betSlipCount.textContent = betSlipSelections.length;
    
    if (betSlipSelections.length === 0) {
        // Show empty state
        betSlipContent.innerHTML = `
            <div class="text-center p-4">
                <i class="bi bi-ticket-perforated" style="font-size: 3rem; color: var(--text-secondary);"></i>
                <p class="text-secondary mt-2 mb-0">Click on odds to add selections</p>
            </div>
        `;
        betSummary.style.display = 'none';
        placeBetBtn.disabled = true;
    } else {
        // Show selections
        let selectionHtml = '';
        betSlipSelections.forEach((sel, index) => {
            selectionHtml += `
                <div class="bet-selection" data-index="${index}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <div class="selection-match">${sel.match}</div>
                            <div class="selection-type">${sel.selection}</div>
                        </div>
                        <div class="text-end">
                            <div class="selection-odds">${sel.odds}</div>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="removeSelection(${index})">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="stake-input-group">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control stake-input" data-index="${index}" 
                                   min="1" max="10000" step="0.01" placeholder="Stake" 
                                   onchange="updateStake(${index}, this.value)">
                        </div>
                    </div>
                </div>
            `;
        });
        
        betSlipContent.innerHTML = selectionHtml;
        betSummary.style.display = 'block';
        updateBetSummary();
    }
}

function removeSelection(index) {
    const selection = betSlipSelections[index];
    
    // Remove selection from array
    betSlipSelections.splice(index, 1);
    
    // Remove selected class from button
    const button = document.querySelector(`[data-selection="${selection.selection}"][data-match="${selection.match}"]`);
    if (button) {
        button.classList.remove('selected');
    }
    
    showNotification('Removed', `${selection.selection} removed from bet slip`, 'info');
    updateBetSlip();
}

function updateStake(index, stake) {
    betSlipSelections[index].stake = parseFloat(stake) || 0;
    updateBetSummary();
}

function updateBetSummary() {
    const totalStakeElement = document.querySelector('.total-stake');
    const totalOddsElement = document.querySelector('.total-odds');
    const potentialReturnElement = document.querySelector('.potential-return');
    const placeBetBtn = document.getElementById('placeBetBtn');
    
    let totalStake = 0;
    let totalOdds = 1;
    let hasStakes = false;
    
    betSlipSelections.forEach(sel => {
        if (sel.stake > 0) {
            totalStake += sel.stake;
            totalOdds *= sel.odds;
            hasStakes = true;
        }
    });
    
    if (!hasStakes) {
        totalOdds = 0;
    }
    
    const potentialReturn = totalStake * totalOdds;
    
    totalStakeElement.textContent = `$${totalStake.toFixed(2)}`;
    totalOddsElement.textContent = totalOdds.toFixed(2);
    potentialReturnElement.textContent = `$${potentialReturn.toFixed(2)}`;
    
    // Enable/disable place bet button
    placeBetBtn.disabled = !hasStakes;
}

// Quick stake buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quick-stake').forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.dataset.amount;
            
            // Apply to all stake inputs
            document.querySelectorAll('.stake-input').forEach(input => {
                input.value = amount;
                const index = parseInt(input.dataset.index);
                updateStake(index, amount);
            });
        });
    });
});

function showNotification(title, message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.style.cssText = 'position: fixed; top: 90px; right: 1rem; z-index: 9999; max-width: 400px;';
    
    notification.innerHTML = `
        <strong>${title}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }
    }, 3000);
}
</script>
@endpush
