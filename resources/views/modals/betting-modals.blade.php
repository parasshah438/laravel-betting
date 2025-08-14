<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h5 class="modal-title fw-bold" id="depositModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Deposit Funds
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <form id="depositForm">
                            <!-- Payment Method Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Choose Payment Method</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="payment_method" id="creditCard" value="credit_card" checked>
                                        <label class="btn btn-outline-primary w-100 p-3" for="creditCard">
                                            <i class="bi bi-credit-card d-block mb-1" style="font-size: 1.5rem;"></i>
                                            <small class="fw-bold">Credit/Debit</small>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="payment_method" id="paypal" value="paypal">
                                        <label class="btn btn-outline-primary w-100 p-3" for="paypal">
                                            <i class="bi bi-paypal d-block mb-1" style="font-size: 1.5rem;"></i>
                                            <small class="fw-bold">PayPal</small>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="payment_method" id="bankTransfer" value="bank_transfer">
                                        <label class="btn btn-outline-primary w-100 p-3" for="bankTransfer">
                                            <i class="bi bi-bank d-block mb-1" style="font-size: 1.5rem;"></i>
                                            <small class="fw-bold">Bank Transfer</small>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="payment_method" id="crypto" value="crypto">
                                        <label class="btn btn-outline-primary w-100 p-3" for="crypto">
                                            <i class="bi bi-currency-bitcoin d-block mb-1" style="font-size: 1.5rem;"></i>
                                            <small class="fw-bold">Crypto</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Selection -->
                            <div class="mb-3">
                                <label for="depositAmount" class="form-label fw-semibold">Deposit Amount</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-success text-white">$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="depositAmount" 
                                           name="amount" 
                                           placeholder="0.00" 
                                           min="10" 
                                           step="0.01"
                                           required>
                                </div>
                                <div class="form-text">Minimum deposit: $10.00 | Maximum: $5,000.00</div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Quick Select</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-success" data-amount="25">$25</button>
                                    <button type="button" class="btn btn-outline-success" data-amount="50">$50</button>
                                    <button type="button" class="btn btn-outline-success" data-amount="100">$100</button>
                                    <button type="button" class="btn btn-outline-success" data-amount="250">$250</button>
                                    <button type="button" class="btn btn-outline-success" data-amount="500">$500</button>
                                    <button type="button" class="btn btn-outline-success" data-amount="1000">$1,000</button>
                                </div>
                            </div>

                            <!-- Bonus Information -->
                            <div class="alert alert-warning border-0 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-gift-fill text-warning me-2" style="font-size: 1.2rem;"></i>
                                    <div class="flex-grow-1">
                                        <strong>Bonus Available!</strong>
                                        <p class="mb-0 small">Deposit $100+ and get 25% bonus up to $250</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Deposit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-credit-card me-2"></i>Deposit Now
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-shield-check text-success me-2"></i>Secure & Fast
                            </h6>
                            
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Instant deposits
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    256-bit SSL encryption
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    No hidden fees
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    24/7 support
                                </li>
                            </ul>

                            <div class="text-center mt-4">
                                <p class="small text-muted mb-2">Payment partners:</p>
                                <div class="d-flex justify-content-around align-items-center">
                                    <i class="bi bi-credit-card text-primary" style="font-size: 1.5rem;"></i>
                                    <i class="bi bi-paypal text-primary" style="font-size: 1.5rem;"></i>
                                    <i class="bi bi-bank text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="withdrawModalLabel">
                    <i class="bi bi-cash-coin me-2"></i>Withdraw Funds
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-wallet2 text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <h6 class="mt-2 mb-0">Available Balance</h6>
                    <h4 class="text-success fw-bold">$1,250.75</h4>
                </div>

                <form id="withdrawForm">
                    <!-- Withdrawal Method -->
                    <div class="mb-3">
                        <label for="withdrawMethod" class="form-label fw-semibold">Withdrawal Method</label>
                        <select class="form-select form-select-lg" id="withdrawMethod" name="method" required>
                            <option value="">Choose method</option>
                            <option value="bank_transfer">Bank Transfer (2-3 days)</option>
                            <option value="paypal">PayPal (24 hours)</option>
                            <option value="check">Check by Mail (7-10 days)</option>
                            <option value="crypto">Cryptocurrency (1 hour)</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="withdrawAmount" class="form-label fw-semibold">Withdrawal Amount</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-danger text-white">$</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="withdrawAmount" 
                                   name="amount" 
                                   placeholder="0.00" 
                                   min="20" 
                                   max="1250.75" 
                                   step="0.01"
                                   required>
                        </div>
                        <div class="form-text">Minimum withdrawal: $20.00</div>
                    </div>

                    <!-- Processing Time Info -->
                    <div class="alert alert-info border-0 mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Processing Time:</strong> Withdrawals are processed within 24-48 hours during business days.
                    </div>

                    <!-- Withdraw Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="bi bi-send me-2"></i>Request Withdrawal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bet Slip Modal -->
<div class="modal fade" id="betSlipModal" tabindex="-1" aria-labelledby="betSlipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="betSlipModalLabel">
                    <i class="bi bi-receipt me-2"></i>Bet Slip
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Bet Type Selection -->
                <div class="p-3 border-bottom">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="betType" id="singleBet" checked>
                        <label class="btn btn-outline-primary" for="singleBet">Single</label>
                        
                        <input type="radio" class="btn-check" name="betType" id="multipleBet">
                        <label class="btn btn-outline-primary" for="multipleBet">Multiple</label>
                        
                        <input type="radio" class="btn-check" name="betType" id="systemBet">
                        <label class="btn btn-outline-primary" for="systemBet">System</label>
                    </div>
                </div>

                <!-- Bet Selections -->
                <div class="p-3" id="betSelections">
                    <!-- Single bet selection example -->
                    <div class="bet-selection-item border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">Chelsea vs Arsenal</h6>
                                <small class="text-muted">Premier League</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Arsenal Win</span>
                            <span class="badge bg-primary">1.85</span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" placeholder="Stake" min="1" step="0.01">
                            <span class="input-group-text">Returns: $0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Bet Summary -->
                <div class="p-3 bg-light border-top">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span>Total Stake:</span>
                                <strong>$0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Odds:</span>
                                <strong>0.00</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <span>Potential Return:</span>
                                <strong class="text-success">$0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Potential Profit:</span>
                                <strong class="text-success">$0.00</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-3">
                        <button type="button" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Place Bet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-warning text-dark border-0">
                <h5 class="modal-title fw-bold" id="notificationsModalLabel">
                    <i class="bi bi-bell me-2"></i>Notifications
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Notification Items -->
                <div class="notification-item p-3 border-bottom">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-trophy text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Bet Won! 🎉</h6>
                            <p class="mb-1 small text-muted">Your bet on Arsenal vs Chelsea won $85.50</p>
                            <small class="text-muted">2 minutes ago</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                <div class="notification-item p-3 border-bottom">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-gift text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">New Bonus Available</h6>
                            <p class="mb-1 small text-muted">Weekend reload bonus: 50% up to $100</p>
                            <small class="text-muted">1 hour ago</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                <div class="notification-item p-3 border-bottom">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-lightning text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Live Match Alert</h6>
                            <p class="mb-1 small text-muted">Lakers vs Celtics is now live!</p>
                            <small class="text-muted">3 hours ago</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                <div class="notification-item p-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-credit-card text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Deposit Successful</h6>
                            <p class="mb-1 small text-muted">$100.00 added to your account</p>
                            <small class="text-muted">1 day ago</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary">Mark All Read</button>
                <button type="button" class="btn btn-primary">View All Notifications</button>
            </div>
        </div>
    </div>
</div>

<!-- Live Chat Modal -->
<div class="modal fade" id="liveChatModal" tabindex="-1" aria-labelledby="liveChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-dark text-white border-0">
                <h5 class="modal-title fw-bold" id="liveChatModalLabel">
                    <i class="bi bi-chat-dots me-2"></i>Live Support Chat
                </h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2">Online</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <!-- Chat Messages -->
                <div class="chat-messages p-3" style="height: 400px; overflow-y: auto;">
                    <div class="text-center mb-3">
                        <small class="text-muted">Chat started at {{ now()->format('H:i') }}</small>
                    </div>
                    
                    <!-- Support Message -->
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-2">
                            <img src="https://via.placeholder.com/40x40/17a2b8/ffffff?text=S" class="rounded-circle" alt="Support">
                        </div>
                        <div class="flex-grow-1">
                            <div class="bg-light rounded p-2 mb-1">
                                <p class="mb-0 small">Hi! I'm Sarah from BetMaster support. How can I help you today?</p>
                            </div>
                            <small class="text-muted">{{ now()->subMinutes(2)->format('H:i') }}</small>
                        </div>
                    </div>

                    <!-- User Message -->
                    <div class="d-flex justify-content-end mb-3">
                        <div class="flex-grow-1 text-end">
                            <div class="bg-primary text-white rounded p-2 mb-1 d-inline-block" style="max-width: 70%;">
                                <p class="mb-0 small">I have a question about my withdrawal</p>
                            </div>
                            <div>
                                <small class="text-muted">{{ now()->subMinutes(1)->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Support Typing Indicator -->
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-2">
                            <img src="https://via.placeholder.com/40x40/17a2b8/ffffff?text=S" class="rounded-circle" alt="Support">
                        </div>
                        <div class="flex-grow-1">
                            <div class="bg-light rounded p-2">
                                <div class="typing-indicator">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="p-3 border-top bg-light">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Type your message..." id="chatInput">
                        <button class="btn btn-primary" type="button">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" title="Attach file">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Emoji">
                                <i class="bi bi-emoji-smile"></i>
                            </button>
                        </div>
                        <small class="text-muted align-self-center">Powered by BetMaster Support</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
