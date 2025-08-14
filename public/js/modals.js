/**
 * Modal Management JavaScript
 * Handles all modal interactions for the betting platform
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    initializeModals();
    
    // Initialize form handlers
    initializeFormHandlers();
    
    // Initialize interactive elements
    initializeInteractiveElements();
});

/**
 * Initialize all modal functionality
 */
function initializeModals() {
    // Password visibility toggles
    initializePasswordToggles();
    
    // Real-time form validation
    initializeFormValidation();
    
    // Modal switching functionality
    initializeModalSwitching();
}

/**
 * Initialize password visibility toggles
 */
function initializePasswordToggles() {
    // Login modal password toggle
    const loginToggle = document.getElementById('toggleModalPassword');
    if (loginToggle) {
        loginToggle.addEventListener('click', function() {
            togglePasswordVisibility('modalPassword', 'toggleModalPasswordIcon');
        });
    }

    // Register modal password toggles
    const registerToggle = document.getElementById('toggleRegisterPassword');
    if (registerToggle) {
        registerToggle.addEventListener('click', function() {
            togglePasswordVisibility('modalRegisterPassword', 'toggleRegisterPassword');
        });
    }
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility(passwordId, iconElement) {
    const passwordField = document.getElementById(passwordId);
    const iconEl = iconElement.querySelector ? iconElement : document.querySelector(`#${iconElement} i, .${iconElement} i`);
    
    if (passwordField && iconEl) {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            iconEl.className = 'bi bi-eye-slash';
        } else {
            passwordField.type = 'password';
            iconEl.className = 'bi bi-eye';
        }
    }
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    // Login form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }

    // Register form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegisterSubmit);
        initializePasswordStrength();
        initializePasswordMatching();
    }

    // Contact form validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactSubmit);
        initializeMessageCounter();
    }

    // Deposit form validation
    const depositForm = document.getElementById('depositForm');
    if (depositForm) {
        depositForm.addEventListener('submit', handleDepositSubmit);
        initializeQuickAmountButtons();
    }

    // Withdraw form validation
    const withdrawForm = document.getElementById('withdrawForm');
    if (withdrawForm) {
        withdrawForm.addEventListener('submit', handleWithdrawSubmit);
    }
}

/**
 * Handle login form submission
 */
function handleLoginSubmit(e) {
    e.preventDefault();
    
    const email = document.getElementById('modalEmail').value;
    const password = document.getElementById('modalPassword').value;
    
    // Basic validation
    if (!email || !password) {
        showFormError('Please fill in all fields');
        return;
    }
    
    if (!isValidEmail(email)) {
        showFormError('Please enter a valid email address');
        return;
    }

    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Signing In...';
    submitBtn.disabled = true;

    // Simulate login process
    setTimeout(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Close modal and show success
        bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
        showSuccessToast('Login successful! Welcome back!');
        
        // Refresh page or update UI
        window.location.reload();
    }, 2000);
}

/**
 * Handle register form submission
 */
function handleRegisterSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const password = formData.get('password');
    const confirmPassword = formData.get('password_confirmation');
    const terms = formData.get('terms');

    // Validate passwords match
    if (password !== confirmPassword) {
        showFormError('Passwords do not match');
        return;
    }

    // Validate terms acceptance
    if (!terms) {
        showFormError('Please accept the terms and conditions');
        return;
    }

    // Validate age
    const dob = new Date(formData.get('date_of_birth'));
    const age = calculateAge(dob);
    if (age < 18) {
        showFormError('You must be 18 or older to register');
        return;
    }

    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Creating Account...';
    submitBtn.disabled = true;

    // Simulate registration process
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
        showSuccessToast('Registration successful! Welcome to BetMaster Pro!');
        
        window.location.reload();
    }, 3000);
}

/**
 * Handle contact form submission
 */
function handleContactSubmit(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Sending...';
    submitBtn.disabled = true;

    // Simulate sending message
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
        showSuccessToast('Message sent successfully! We\'ll get back to you soon.');
        
        // Reset form
        e.target.reset();
    }, 2000);
}

/**
 * Handle deposit form submission
 */
function handleDepositSubmit(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('depositAmount').value);
    
    if (amount < 10) {
        showFormError('Minimum deposit amount is $10.00');
        return;
    }

    if (amount > 5000) {
        showFormError('Maximum deposit amount is $5,000.00');
        return;
    }

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Processing...';
    submitBtn.disabled = true;

    // Simulate deposit processing
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        bootstrap.Modal.getInstance(document.getElementById('depositModal')).hide();
        showSuccessToast(`Deposit of $${amount.toFixed(2)} processed successfully!`);
        
        // Update balance display
        updateBalance();
    }, 3000);
}

/**
 * Handle withdraw form submission
 */
function handleWithdrawSubmit(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('withdrawAmount').value);
    const availableBalance = 1250.75; // This would come from backend
    
    if (amount < 20) {
        showFormError('Minimum withdrawal amount is $20.00');
        return;
    }

    if (amount > availableBalance) {
        showFormError('Insufficient funds');
        return;
    }

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Processing...';
    submitBtn.disabled = true;

    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        bootstrap.Modal.getInstance(document.getElementById('withdrawModal')).hide();
        showSuccessToast(`Withdrawal of $${amount.toFixed(2)} requested successfully!`);
    }, 2500);
}

/**
 * Initialize password strength indicator
 */
function initializePasswordStrength() {
    const passwordField = document.getElementById('modalRegisterPassword');
    const strengthIndicator = document.getElementById('passwordStrength');
    
    if (passwordField && strengthIndicator) {
        passwordField.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updatePasswordStrength(strengthIndicator, strength);
        });
    }
}

/**
 * Calculate password strength
 */
function calculatePasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score += 1;
    if (password.match(/[a-z]/)) score += 1;
    if (password.match(/[A-Z]/)) score += 1;
    if (password.match(/[0-9]/)) score += 1;
    if (password.match(/[^a-zA-Z0-9]/)) score += 1;
    
    return score;
}

/**
 * Update password strength indicator
 */
function updatePasswordStrength(element, strength) {
    element.style.height = '4px';
    element.style.borderRadius = '2px';
    element.style.marginTop = '5px';
    
    if (strength <= 2) {
        element.style.backgroundColor = '#dc3545';
        element.style.width = '33%';
    } else if (strength <= 4) {
        element.style.backgroundColor = '#ffc107';
        element.style.width = '66%';
    } else {
        element.style.backgroundColor = '#28a745';
        element.style.width = '100%';
    }
}

/**
 * Initialize password matching validation
 */
function initializePasswordMatching() {
    const passwordField = document.getElementById('modalRegisterPassword');
    const confirmField = document.getElementById('modalRegisterPasswordConfirm');
    
    if (passwordField && confirmField) {
        confirmField.addEventListener('input', function() {
            if (passwordField.value !== this.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
}

/**
 * Initialize message character counter
 */
function initializeMessageCounter() {
    const messageField = document.getElementById('contactMessage');
    const counterElement = document.getElementById('messageCount');
    
    if (messageField && counterElement) {
        messageField.addEventListener('input', function() {
            const count = this.value.length;
            counterElement.textContent = count;
            
            if (count > 500) {
                counterElement.classList.add('text-danger');
                this.classList.add('is-invalid');
            } else {
                counterElement.classList.remove('text-danger');
                this.classList.remove('is-invalid');
            }
        });
    }
}

/**
 * Initialize quick amount buttons for deposit
 */
function initializeQuickAmountButtons() {
    const amountButtons = document.querySelectorAll('[data-amount]');
    const amountField = document.getElementById('depositAmount');
    
    amountButtons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            if (amountField) {
                amountField.value = amount;
                amountField.focus();
            }
        });
    });
}

/**
 * Initialize modal switching functionality
 */
function initializeModalSwitching() {
    // Handle modal-to-modal transitions
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-bs-toggle="modal"]')) {
            const currentModal = e.target.closest('.modal');
            if (currentModal) {
                const modalInstance = bootstrap.Modal.getInstance(currentModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        }
    });
}

/**
 * Initialize interactive elements
 */
function initializeInteractiveElements() {
    // Live chat functionality
    initializeLiveChat();
    
    // Notification management
    initializeNotifications();
    
    // Bet slip functionality
    initializeBetSlip();
}

/**
 * Initialize live chat functionality
 */
function initializeLiveChat() {
    const chatInput = document.getElementById('chatInput');
    const chatSendBtn = document.querySelector('#liveChatModal .btn-primary');
    
    if (chatInput && chatSendBtn) {
        chatSendBtn.addEventListener('click', sendChatMessage);
        
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChatMessage();
            }
        });
    }
}

/**
 * Send chat message
 */
function sendChatMessage() {
    const chatInput = document.getElementById('chatInput');
    const messagesContainer = document.querySelector('.chat-messages');
    
    if (chatInput.value.trim()) {
        // Add user message
        const userMessage = createChatMessage(chatInput.value, 'user');
        messagesContainer.appendChild(userMessage);
        
        // Clear input
        chatInput.value = '';
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Simulate support response
        setTimeout(() => {
            const supportMessage = createChatMessage('Thank you for your message. Let me help you with that.', 'support');
            messagesContainer.appendChild(supportMessage);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 1500);
    }
}

/**
 * Create chat message element
 */
function createChatMessage(text, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `d-flex mb-3 ${type === 'user' ? 'justify-content-end' : ''}`;
    
    const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    if (type === 'user') {
        messageDiv.innerHTML = `
            <div class="flex-grow-1 text-end">
                <div class="bg-primary text-white rounded p-2 mb-1 d-inline-block" style="max-width: 70%;">
                    <p class="mb-0 small">${text}</p>
                </div>
                <div>
                    <small class="text-muted">${time}</small>
                </div>
            </div>
        `;
    } else {
        messageDiv.innerHTML = `
            <div class="flex-shrink-0 me-2">
                <img src="https://via.placeholder.com/40x40/17a2b8/ffffff?text=S" class="rounded-circle" alt="Support">
            </div>
            <div class="flex-grow-1">
                <div class="bg-light rounded p-2 mb-1">
                    <p class="mb-0 small">${text}</p>
                </div>
                <small class="text-muted">${time}</small>
            </div>
        `;
    }
    
    return messageDiv;
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    const dismissButtons = document.querySelectorAll('#notificationsModal .btn-outline-secondary');
    
    dismissButtons.forEach(button => {
        button.addEventListener('click', function() {
            const notificationItem = this.closest('.notification-item');
            if (notificationItem) {
                notificationItem.style.opacity = '0.5';
                setTimeout(() => {
                    notificationItem.remove();
                }, 300);
            }
        });
    });
}

/**
 * Initialize bet slip functionality
 */
function initializeBetSlip() {
    const betTypeRadios = document.querySelectorAll('input[name="betType"]');
    
    betTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateBetSlipDisplay(this.value);
        });
    });
}

/**
 * Update bet slip display based on bet type
 */
function updateBetSlipDisplay(betType) {
    const betSelections = document.getElementById('betSelections');
    // Update UI based on bet type (single, multiple, system)
    console.log('Bet type changed to:', betType);
}

/**
 * Utility Functions
 */

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function calculateAge(birthDate) {
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

function showFormError(message) {
    // Create or update error alert
    let errorAlert = document.querySelector('.modal.show .alert-danger');
    
    if (!errorAlert) {
        errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger alert-dismissible fade show';
        errorAlert.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>
            <span class="error-message">${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const modalBody = document.querySelector('.modal.show .modal-body');
        modalBody.insertBefore(errorAlert, modalBody.firstChild);
    } else {
        errorAlert.querySelector('.error-message').textContent = message;
    }
}

function showSuccessToast(message) {
    // Create success toast
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function updateBalance() {
    // Update balance display in header
    const balanceElements = document.querySelectorAll('.user-balance');
    balanceElements.forEach(element => {
        // This would fetch real balance from API
        element.textContent = '$1,350.75';
    });
}
