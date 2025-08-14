// BetMaster Pro - Main JavaScript Functions
class BettingPlatform {
    constructor() {
        this.betSlip = [];
        this.totalOdds = 1;
        this.totalStake = 0;
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateBetSlip();
    }

    bindEvents() {
        // Odds button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.odds-btn')) {
                this.handleOddsClick(e.target.closest('.odds-btn'));
            }
        });

        // Quick stake buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-stake')) {
                const amount = parseFloat(e.target.dataset.amount);
                this.setQuickStake(amount);
            }
        });

        // Stake input changes
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('stake-input')) {
                this.updateStakeInput(e.target);
            }
        });

        // Remove selection
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-selection')) {
                const index = parseInt(e.target.dataset.index);
                this.removeSelection(index);
            }
        });

        // Place bet button
        const placeBetBtn = document.getElementById('placeBetBtn');
        if (placeBetBtn) {
            placeBetBtn.addEventListener('click', () => this.placeBet());
        }

        // Sport category clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.sport-category')) {
                this.handleSportCategoryClick(e.target.closest('.sport-category'));
            }
        });
    }

    handleOddsClick(oddsBtn) {
        const selection = oddsBtn.dataset.selection;
        const odds = parseFloat(oddsBtn.dataset.odds);
        const matchElement = oddsBtn.closest('.match-card');
        const teams = this.getMatchTeams(matchElement);
        
        // Check if already selected
        const existingIndex = this.betSlip.findIndex(bet => 
            bet.selection === selection && bet.match === teams
        );

        if (existingIndex !== -1) {
            // Remove if already selected
            this.removeSelection(existingIndex);
            oddsBtn.classList.remove('selected');
        } else {
            // Add new selection
            this.addSelection({
                match: teams,
                selection: selection,
                odds: odds,
                stake: 0
            });
            oddsBtn.classList.add('selected');
        }

        this.updateBetSlip();
        this.showNotification(`${selection} ${existingIndex !== -1 ? 'removed from' : 'added to'} bet slip`, 'success');
    }

    getMatchTeams(matchElement) {
        const teamNames = matchElement.querySelectorAll('.team-name');
        if (teamNames.length >= 2) {
            return `${teamNames[0].textContent} vs ${teamNames[1].textContent}`;
        }
        return 'Unknown Match';
    }

    addSelection(selection) {
        this.betSlip.push(selection);
        this.calculateTotals();
    }

    removeSelection(index) {
        if (index >= 0 && index < this.betSlip.length) {
            this.betSlip.splice(index, 1);
            this.calculateTotals();
            this.updateBetSlip();
        }
    }

    calculateTotals() {
        this.totalOdds = this.betSlip.reduce((acc, bet) => acc * bet.odds, 1);
        this.totalStake = this.betSlip.reduce((acc, bet) => acc + (bet.stake || 0), 0);
    }

    updateBetSlip() {
        const betSlipContent = document.getElementById('betSlipContent');
        const betSlipCount = document.querySelector('.bet-slip-count');
        const betSummary = document.getElementById('betSummary');

        if (!betSlipContent) return;

        // Update count
        betSlipCount.textContent = this.betSlip.length;

        if (this.betSlip.length === 0) {
            betSlipContent.innerHTML = `
                <div class="text-center p-4">
                    <i class="bi bi-ticket-perforated" style="font-size: 3rem; color: var(--text-secondary);"></i>
                    <p class="text-secondary mt-2 mb-0">Click on odds to add selections</p>
                </div>
            `;
            betSummary.style.display = 'none';
            return;
        }

        let slipHTML = '';
        this.betSlip.forEach((bet, index) => {
            slipHTML += `
                <div class="bet-selection">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="selection-match">${bet.match}</div>
                        <button class="btn btn-sm btn-outline-danger remove-selection" data-index="${index}">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="selection-bet">${bet.selection}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="selection-odds">${bet.odds}</span>
                    </div>
                    <input type="number" class="stake-input" placeholder="Enter stake" 
                           value="${bet.stake || ''}" data-index="${index}" min="1" max="10000">
                </div>
            `;
        });

        betSlipContent.innerHTML = slipHTML;

        // Show summary if bets exist
        betSummary.style.display = 'block';
        this.updateSummary();
    }

    updateSummary() {
        const totalStakeElement = document.querySelector('.total-stake');
        const totalOddsElement = document.querySelector('.total-odds');
        const potentialReturnElement = document.querySelector('.potential-return');
        const placeBetBtn = document.getElementById('placeBetBtn');

        if (totalStakeElement) totalStakeElement.textContent = `$${this.totalStake.toFixed(2)}`;
        if (totalOddsElement) totalOddsElement.textContent = this.totalOdds.toFixed(2);
        
        const potentialReturn = this.totalStake * this.totalOdds;
        if (potentialReturnElement) potentialReturnElement.textContent = `$${potentialReturn.toFixed(2)}`;

        // Enable/disable place bet button
        if (placeBetBtn) {
            placeBetBtn.disabled = this.totalStake <= 0 || this.betSlip.length === 0;
        }
    }

    updateStakeInput(input) {
        const index = parseInt(input.dataset.index);
        const stake = parseFloat(input.value) || 0;
        
        if (index >= 0 && index < this.betSlip.length) {
            this.betSlip[index].stake = stake;
            this.calculateTotals();
            this.updateSummary();
        }
    }

    setQuickStake(amount) {
        // Set stake for all selections
        this.betSlip.forEach(bet => bet.stake = amount);
        this.calculateTotals();
        this.updateBetSlip();
    }

    async placeBet() {
        if (this.betSlip.length === 0 || this.totalStake <= 0) {
            this.showNotification('Please add selections and set stakes', 'error');
            return;
        }

        const placeBetBtn = document.getElementById('placeBetBtn');
        const originalText = placeBetBtn.innerHTML;
        
        // Show loading
        placeBetBtn.innerHTML = '<span class="loading-spinner"></span> Placing Bet...';
        placeBetBtn.disabled = true;

        try {
            // Simulate API call
            await this.simulateApiCall();
            
            // Success
            this.showNotification('Bet placed successfully!', 'success');
            this.clearBetSlip();
            
            // Update balance (simulate)
            this.updateBalance(-this.totalStake);

        } catch (error) {
            this.showNotification('Failed to place bet. Please try again.', 'error');
        } finally {
            placeBetBtn.innerHTML = originalText;
            placeBetBtn.disabled = false;
        }
    }

    clearBetSlip() {
        this.betSlip = [];
        this.totalOdds = 1;
        this.totalStake = 0;
        
        // Remove selected class from odds buttons
        document.querySelectorAll('.odds-btn.selected').forEach(btn => {
            btn.classList.remove('selected');
        });
        
        this.updateBetSlip();
    }

    handleSportCategoryClick(categoryElement) {
        // Remove active class from all categories
        document.querySelectorAll('.sport-category').forEach(cat => {
            cat.classList.remove('active');
        });
        
        // Add active class to clicked category
        categoryElement.classList.add('active');
        
        const sportName = categoryElement.querySelector('span').textContent;
        this.showNotification(`Switched to ${sportName}`, 'success');
        
        // Here you would typically load matches for the selected sport
        this.loadSportMatches(sportName);
    }

    async loadSportMatches(sport) {
        // Simulate loading matches for selected sport
        console.log(`Loading matches for ${sport}`);
    }

    simulateApiCall() {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // 90% success rate for demo
                if (Math.random() > 0.1) {
                    resolve();
                } else {
                    reject(new Error('API Error'));
                }
            }, 2000);
        });
    }

    updateBalance(amount) {
        const balanceElement = document.querySelector('.balance-display');
        if (balanceElement) {
            const currentBalance = parseFloat(balanceElement.textContent.replace(/[$,]/g, ''));
            const newBalance = currentBalance + amount;
            balanceElement.innerHTML = `<i class="bi bi-wallet2"></i> $${newBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        }
    }

    showNotification(message, type = 'success') {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);

        // Hide and remove notification
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Live Odds Updates
class LiveOddsUpdater {
    constructor() {
        this.updateInterval = null;
        this.isActive = false;
    }

    start() {
        if (this.isActive) return;
        
        this.isActive = true;
        this.updateInterval = setInterval(() => {
            this.updateLiveOdds();
        }, 3000); // Update every 3 seconds
    }

    stop() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
        this.isActive = false;
    }

    updateLiveOdds() {
        const liveMatches = document.querySelectorAll('.live-match');
        
        liveMatches.forEach(match => {
            const oddsButtons = match.querySelectorAll('.odds-btn');
            
            oddsButtons.forEach(btn => {
                const currentOdds = parseFloat(btn.querySelector('.odds-value').textContent);
                
                // Simulate small odds fluctuations (±0.05)
                const change = (Math.random() - 0.5) * 0.1;
                const newOdds = Math.max(1.01, currentOdds + change);
                
                btn.querySelector('.odds-value').textContent = newOdds.toFixed(2);
                btn.dataset.odds = newOdds.toFixed(2);
                
                // Add flash effect for changes
                if (Math.abs(change) > 0.02) {
                    btn.classList.add('odds-changed');
                    setTimeout(() => btn.classList.remove('odds-changed'), 1000);
                }
            });
        });
    }
}

// Real-time Match Updates
class MatchUpdater {
    constructor() {
        this.updateInterval = null;
    }

    start() {
        this.updateInterval = setInterval(() => {
            this.updateLiveScores();
        }, 10000); // Update every 10 seconds
    }

    stop() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    }

    updateLiveScores() {
        const liveMatches = document.querySelectorAll('.live-match');
        
        liveMatches.forEach(match => {
            const scoreElement = match.querySelector('.match-score');
            const minuteElement = match.querySelector('.match-minute');
            
            if (scoreElement && minuteElement) {
                // Simulate minute progression
                const currentMinute = parseInt(minuteElement.textContent);
                if (currentMinute < 90) {
                    minuteElement.textContent = `${currentMinute + 1}'`;
                }
                
                // Rarely update scores (5% chance)
                if (Math.random() < 0.05) {
                    const scores = scoreElement.textContent.split(' - ');
                    if (scores.length === 2) {
                        const homeScore = parseInt(scores[0]);
                        const awayScore = parseInt(scores[1]);
                        
                        // Random goal
                        if (Math.random() < 0.5) {
                            scoreElement.textContent = `${homeScore + 1} - ${awayScore}`;
                        } else {
                            scoreElement.textContent = `${homeScore} - ${awayScore + 1}`;
                        }
                        
                        // Flash effect
                        scoreElement.classList.add('score-updated');
                        setTimeout(() => scoreElement.classList.remove('score-updated'), 2000);
                    }
                }
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize main betting platform
    const bettingPlatform = new BettingPlatform();
    
    // Initialize live updates
    const liveOddsUpdater = new LiveOddsUpdater();
    const matchUpdater = new MatchUpdater();
    
    // Start live updates if there are live matches
    if (document.querySelector('.live-match')) {
        liveOddsUpdater.start();
        matchUpdater.start();
    }
    
    // Global functions for external use
    window.bettingPlatform = bettingPlatform;
    window.liveOddsUpdater = liveOddsUpdater;
    window.matchUpdater = matchUpdater;
});

// Utility Functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatOdds(odds) {
    return odds.toFixed(2);
}

function calculateImpliedProbability(odds) {
    return ((1 / odds) * 100).toFixed(1) + '%';
}

// Add CSS for dynamic effects
const dynamicStyles = `
<style>
.odds-changed {
    animation: oddsFlash 1s ease-in-out;
}

@keyframes oddsFlash {
    0% { background-color: var(--odds-bg); }
    50% { background-color: var(--warning-color); }
    100% { background-color: var(--odds-bg); }
}

.score-updated {
    animation: scoreFlash 2s ease-in-out;
}

@keyframes scoreFlash {
    0% { color: var(--accent-color); transform: scale(1); }
    50% { color: var(--success-color); transform: scale(1.1); }
    100% { color: var(--accent-color); transform: scale(1); }
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', dynamicStyles);
