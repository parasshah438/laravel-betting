// Modal System JavaScript - Bootstrap 5 Enhanced
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all modal functionality
    initializeAuthModals();
    initializeBettingModals();
    initializeFormValidation();
    initializePasswordStrength();
    initializeInteractiveElements();
    
    // Authentication Modals Functionality
    function initializeAuthModals() {
        // Social login handlers
        document.querySelectorAll('.btn-social').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const provider = this.dataset.provider;
                handleSocialLogin(provider);
            });
        });
        
        // Switch between login and register modals
        const switchToRegister = document.getElementById('switchToRegister');
        const switchToLogin = document.getElementById('switchToLogin');
        
        if (switchToRegister) {
            switchToRegister.addEventListener('click', function(e) {
                e.preventDefault();
                bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
                setTimeout(() => {
                    new bootstrap.Modal(document.getElementById('registerModal')).show();
                }, 300);
            });
        }
        
        if (switchToLogin) {
            switchToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
                setTimeout(() => {
                    new bootstrap.Modal(document.getElementById('loginModal')).show();
                }, 300);
            });
        }
        
        // Forgot password modal
        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
                setTimeout(() => {
                    new bootstrap.Modal(document.getElementById('forgotPasswordModal')).show();
                }, 300);
            });
        }
    }
    
    // Betting Modals Functionality
    function initializeBettingModals() {
        // Quick amount selection
        document.querySelectorAll('.amount-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update input field
                const modal = this.closest('.modal');
                const amountInput = modal.querySelector('input[type="number"]');
                if (amountInput) {
                    amountInput.value = this.dataset.amount;
                    amountInput.dispatchEvent(new Event('input'));
                }
            });
        });
        
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Remove active class from all methods
                const container = this.closest('.payment-methods');
                container.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
                // Add active class to clicked method
                this.classList.add('active');
                
                // Update hidden input or form data
                const modal = this.closest('.modal');
                const methodInput = modal.querySelector('input[name="payment_method"]');
                if (methodInput) {
                    methodInput.value = this.dataset.method;
                }
            });
        });
        
        // Bet slip functionality
        initializeBetSlip();
        
        // Live chat functionality
        initializeLiveChat();
        
        // Notification handling
        initializeNotifications();
    }
    
    // Form Validation
    function initializeFormValidation() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (form.checkValidity()) {
                    handleFormSubmission(form);
                } else {
                    form.classList.add('was-validated');
                    // Find first invalid field and focus it
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }
            });
        });
        
        // Real-time validation for inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
    }
    
    // Password Strength Indicator
    function initializePasswordStrength() {
        const passwordInputs = document.querySelectorAll('input[type="password"][data-strength="true"]');
        
        passwordInputs.forEach(input => {
            const container = input.closest('.form-floating');
            if (!container.querySelector('.password-strength')) {
                const strengthHtml = `
                    <div class="password-strength">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <div class="strength-text"></div>
                `;
                container.insertAdjacentHTML('beforeend', strengthHtml);
            }
            
            input.addEventListener('input', function() {
                updatePasswordStrength(this);
            });
        });
    }
    
    // Interactive Elements
    function initializeInteractiveElements() {
        // Password visibility toggle
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });
        
        // Character counter for textareas
        document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
            const maxLength = parseInt(textarea.getAttribute('maxlength'));
            const counter = document.createElement('div');
            counter.className = 'character-counter text-muted mt-2';
            counter.style.fontSize = '0.875rem';
            textarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${remaining} characters remaining`;
                counter.style.color = remaining < 50 ? '#dc3545' : '#6c757d';
            }
            
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
        
        // Auto-resize textareas
        document.querySelectorAll('textarea[data-auto-resize="true"]').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    }
    
    // Bet Slip Functionality
    function initializeBetSlip() {
        const betTypeRadios = document.querySelectorAll('input[name="bet_type"]');
        const stakeInputs = document.querySelectorAll('.stake-input');
        const potentialWinDisplay = document.getElementById('potentialWin');
        
        // Update potential win calculation
        function updatePotentialWin() {
            const selectedType = document.querySelector('input[name="bet_type"]:checked');
            if (!selectedType) return;
            
            let totalStake = 0;
            let totalOdds = 1;
            
            stakeInputs.forEach(input => {
                const stake = parseFloat(input.value) || 0;
                totalStake += stake;
                
                const odds = parseFloat(input.dataset.odds) || 1;
                if (selectedType.value === 'multiple') {
                    totalOdds *= odds;
                } else {
                    totalOdds = odds; // For single bets
                }
            });
            
            const potentialWin = totalStake * totalOdds;
            if (potentialWinDisplay) {
                potentialWinDisplay.textContent = `$${potentialWin.toFixed(2)}`;
            }
        }
        
        betTypeRadios.forEach(radio => {
            radio.addEventListener('change', updatePotentialWin);
        });
        
        stakeInputs.forEach(input => {
            input.addEventListener('input', updatePotentialWin);
        });
        
        // Initialize calculation
        updatePotentialWin();
    }
    
    // Live Chat Functionality
    function initializeLiveChat() {
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        
        if (!chatForm) return;
        
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Add user message to chat
            addChatMessage('You', message, true);
            messageInput.value = '';
            
            // Simulate agent response (replace with real chat integration)
            setTimeout(() => {
                const responses = [
                    "Thank you for contacting us! How can I help you today?",
                    "I'll be happy to assist you with that. Let me check that for you.",
                    "Is there anything else I can help you with?",
                    "Thank you for your patience. Here's what I found..."
                ];
                const response = responses[Math.floor(Math.random() * responses.length)];
                addChatMessage('Support Agent', response, false);
            }, 1000 + Math.random() * 2000);
        });
        
        function addChatMessage(sender, message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${isUser ? 'user-message' : ''}`;
            
            const avatar = isUser ? 'U' : 'S';
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            messageDiv.innerHTML = `
                <div class="message-avatar">${avatar}</div>
                <div class="message-content">
                    <div class="message-sender">${sender}</div>
                    <div class="message-text">${message}</div>
                    <div class="message-time">${time}</div>
                </div>
            `;
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
    
    // Notifications
    function initializeNotifications() {
        // Mark notification as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                this.style.opacity = '0.7';
                const notificationId = this.dataset.id;
                if (notificationId) {
                    markNotificationAsRead(notificationId);
                }
            });
        });
    }
    
    // Utility Functions
    function handleSocialLogin(provider) {
        console.log(`Initiating ${provider} login...`);
        // Implement social login logic here
        // This would typically redirect to the OAuth provider
        window.location.href = `/auth/${provider}`;
    }
    
    function validateField(field) {
        const value = field.value.trim();
        const fieldType = field.type;
        const fieldName = field.name;
        
        let isValid = true;
        let message = '';
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'This field is required.';
        }
        
        // Email validation
        else if (fieldType === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Please enter a valid email address.';
            }
        }
        
        // Password validation
        else if (fieldType === 'password' && fieldName === 'password' && value) {
            if (value.length < 8) {
                isValid = false;
                message = 'Password must be at least 8 characters long.';
            }
        }
        
        // Confirm password validation
        else if (fieldName === 'password_confirmation' && value) {
            const passwordField = document.querySelector('input[name="password"]');
            if (passwordField && value !== passwordField.value) {
                isValid = false;
                message = 'Passwords do not match.';
            }
        }
        
        // Age validation
        else if (fieldName === 'date_of_birth' && value) {
            const birthDate = new Date(value);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            if (age < 18) {
                isValid = false;
                message = 'You must be at least 18 years old to register.';
            }
        }
        
        // Update field validation state
        field.classList.remove('is-valid', 'is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        
        if (isValid) {
            field.classList.add('is-valid');
            if (feedback) {
                feedback.textContent = '';
                feedback.style.display = 'none';
            }
        } else {
            field.classList.add('is-invalid');
            if (feedback) {
                feedback.textContent = message;
                feedback.style.display = 'block';
            } else {
                // Create feedback element
                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback';
                feedbackDiv.textContent = message;
                field.parentNode.appendChild(feedbackDiv);
            }
        }
        
        return isValid;
    }
    
    function updatePasswordStrength(passwordInput) {
        const password = passwordInput.value;
        const container = passwordInput.closest('.form-floating');
        const strengthBars = container.querySelectorAll('.strength-bar');
        const strengthText = container.querySelector('.strength-text');
        
        let strength = 0;
        let strengthLabel = '';
        
        // Calculate password strength
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        // Reset bars
        strengthBars.forEach(bar => bar.classList.remove('active'));
        
        // Update strength display
        switch (strength) {
            case 0:
            case 1:
                strengthLabel = 'Weak';
                strengthText.className = 'strength-text strength-weak';
                break;
            case 2:
                strengthLabel = 'Fair';
                strengthText.className = 'strength-text strength-fair';
                strengthBars[0].classList.add('active');
                break;
            case 3:
                strengthLabel = 'Good';
                strengthText.className = 'strength-text strength-good';
                strengthBars[0].classList.add('active');
                strengthBars[1].classList.add('active');
                break;
            case 4:
            case 5:
                strengthLabel = 'Strong';
                strengthText.className = 'strength-text strength-strong';
                strengthBars.forEach(bar => bar.classList.add('active'));
                break;
        }
        
        strengthText.textContent = password ? `Password Strength: ${strengthLabel}` : '';
    }
    
    function handleFormSubmission(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Show loading state
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
        
        // Get form action and method
        const action = form.getAttribute('action') || window.location.href;
        const method = form.getAttribute('method') || 'POST';
        
        // Add CSRF token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }
        
        // Submit form via AJAX
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok');
        })
        .then(data => {
            // Handle successful submission
            if (data.success) {
                showNotification('Success!', data.message || 'Operation completed successfully.', 'success');
                
                // Close modal after short delay
                setTimeout(() => {
                    const modal = form.closest('.modal');
                    if (modal) {
                        bootstrap.Modal.getInstance(modal).hide();
                    }
                    
                    // Redirect if specified
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Reload page to update authentication state
                        window.location.reload();
                    }
                }, 1500);
            } else {
                showNotification('Error', data.message || 'Something went wrong.', 'error');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            showNotification('Error', 'An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }
    
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
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
    
    function markNotificationAsRead(notificationId) {
        // Send AJAX request to mark notification as read
        fetch('/api/notifications/' + notificationId + '/read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Notification marked as read:', data);
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Global modal event handlers
    document.addEventListener('show.bs.modal', function(e) {
        const modal = e.target;
        const form = modal.querySelector('form');
        
        // Reset form when modal opens
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
            
            // Clear validation states
            form.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
            
            form.querySelectorAll('.invalid-feedback, .valid-feedback').forEach(feedback => {
                feedback.style.display = 'none';
            });
        }
        
        // Focus first input
        setTimeout(() => {
            const firstInput = modal.querySelector('.form-control:not([readonly]):not([disabled])');
            if (firstInput) {
                firstInput.focus();
            }
        }, 300);
    });
    
    // Handle modal backdrop clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            // Optional: prevent modal close on backdrop click for important modals
            const modal = e.target;
            if (modal.dataset.backdrop === 'static') {
                e.stopPropagation();
            }
        }
    });
});

// Export functions for external use
window.BettingModals = {
    showLoginModal: () => new bootstrap.Modal(document.getElementById('loginModal')).show(),
    showRegisterModal: () => new bootstrap.Modal(document.getElementById('registerModal')).show(),
    showDepositModal: () => new bootstrap.Modal(document.getElementById('depositModal')).show(),
    showWithdrawModal: () => new bootstrap.Modal(document.getElementById('withdrawModal')).show(),
    showBetSlipModal: () => new bootstrap.Modal(document.getElementById('betSlipModal')).show(),
    showContactModal: () => new bootstrap.Modal(document.getElementById('contactModal')).show(),
    showNotificationsModal: () => new bootstrap.Modal(document.getElementById('notificationsModal')).show(),
    showLiveChatModal: () => new bootstrap.Modal(document.getElementById('liveChatModal')).show()
};
