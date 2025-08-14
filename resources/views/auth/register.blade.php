@extends('layouts.app')

@section('title', 'Register - BetMaster Pro')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Registration Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary mb-2">Join BetMaster Pro</h2>
                        <p class="text-secondary mb-0">Create your account and start betting today</p>
                    </div>

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <!-- Full Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter your full name" 
                                       required 
                                       autocomplete="name" 
                                       autofocus>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter your email" 
                                       required 
                                       autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Field -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="Enter your phone number" 
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date of Birth Field -->
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-calendar"></i>
                                </span>
                                <input type="date" 
                                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" 
                                       name="date_of_birth" 
                                       value="{{ old('date_of_birth') }}" 
                                       required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">You must be 18 or older to register</div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Create a strong password" 
                                       required 
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Password must be at least 8 characters long</div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm your password" 
                                       required 
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" 
                                       name="terms" 
                                       id="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    I want to receive promotional emails and updates
                                </label>
                            </div>
                        </div>

                        <!-- Register Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>
                        </div>

                        <!-- OR Divider -->
                        <div class="text-center mb-3">
                            <div class="position-relative">
                                <hr>
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-secondary">OR</span>
                            </div>
                        </div>

                        <!-- Social Registration Buttons -->
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-google me-1"></i>Google
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-facebook me-1"></i>Facebook
                                </button>
                            </div>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="mb-0">Already have an account? 
                                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Features -->
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="text-center text-white">
                        <i class="bi bi-shield-lock" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Secure Registration</h6>
                        <small class="opacity-75">Your data is protected</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center text-white">
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Instant Verification</h6>
                        <small class="opacity-75">Quick account setup</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center text-white">
                        <i class="bi bi-gift" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Welcome Bonus</h6>
                        <small class="opacity-75">100% up to $200</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        password.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Toggle password confirmation visibility
document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
    const password = document.getElementById('password_confirmation');
    const icon = document.getElementById('togglePasswordConfirmIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        password.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const terms = document.getElementById('terms');
    
    // Check if passwords match
    if (password.value !== confirmPassword.value) {
        e.preventDefault();
        confirmPassword.classList.add('is-invalid');
        alert('Passwords do not match!');
        return;
    }
    
    // Check terms acceptance
    if (!terms.checked) {
        e.preventDefault();
        terms.classList.add('is-invalid');
        alert('Please accept the terms and conditions!');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Creating Account...';
    submitBtn.disabled = true;
});

// Real-time password matching
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password');
    const confirmPassword = this;
    
    if (password.value !== confirmPassword.value) {
        confirmPassword.classList.add('is-invalid');
    } else {
        confirmPassword.classList.remove('is-invalid');
    }
});

// Clear validation errors on input
['name', 'email', 'phone', 'password'].forEach(function(fieldName) {
    document.getElementById(fieldName).addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});

// Age validation
document.getElementById('date_of_birth').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age < 18) {
        this.classList.add('is-invalid');
        alert('You must be 18 or older to register!');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>
@endsection

@section('styles')
<style>
/* Custom registration page styles */
.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.form-control:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.input-group-text {
    background: transparent;
    border-right: none;
}

.form-control {
    border-left: none;
}

.form-control:focus {
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: #6f42c1;
    color: #6f42c1;
}

/* Animation for cards */
.card {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
    
    .container-fluid {
        padding: 1rem;
    }
}

/* Password strength indicator */
.password-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 5px;
    transition: all 0.3s ease;
}

.strength-weak { background-color: #dc3545; }
.strength-medium { background-color: #ffc107; }
.strength-strong { background-color: #28a745; }
</style>
@endsection
