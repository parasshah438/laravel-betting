@extends('layouts.app')

@section('title', 'Login - BetMaster Pro')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-5 col-md-7 col-sm-9">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Login Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary mb-2">Welcome Back!</h2>
                        <p class="text-secondary mb-0">Sign in to your BetMaster Pro account</p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
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
                                       autocomplete="email" 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
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
                                       placeholder="Enter your password" 
                                       required 
                                       autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-primary text-decoration-none">
                                        Forgot Password?
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </div>

                        <!-- OR Divider -->
                        <div class="text-center mb-3">
                            <div class="position-relative">
                                <hr>
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-secondary">OR</span>
                            </div>
                        </div>

                        <!-- Social Login Buttons -->
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

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="mb-0">Don't have an account? 
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Sign Up</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="text-center text-white">
                        <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Secure Login</h6>
                        <small class="opacity-75">256-bit SSL encryption</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center text-white">
                        <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">24/7 Support</h6>
                        <small class="opacity-75">Round-the-clock assistance</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center text-white">
                        <i class="bi bi-award" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Licensed</h6>
                        <small class="opacity-75">Fully regulated platform</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Account Modal -->
<div class="modal fade" id="demoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Demo Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Try our platform with demo credentials:</p>
                <div class="alert alert-info">
                    <strong>Email:</strong> demo@betmaster.com<br>
                    <strong>Password:</strong> demo123
                </div>
                <p class="mb-0"><small class="text-muted">Note: This is for demonstration purposes only.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="fillDemoCredentials()">Use Demo</button>
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

// Fill demo credentials
function fillDemoCredentials() {
    document.getElementById('email').value = 'demo@betmaster.com';
    document.getElementById('password').value = 'demo123';
    const modal = bootstrap.Modal.getInstance(document.getElementById('demoModal'));
    modal.hide();
}

// Show demo modal on load (for demonstration purposes)
document.addEventListener('DOMContentLoaded', function() {
    // Uncomment the next line to show demo modal on page load
    // const demoModal = new bootstrap.Modal(document.getElementById('demoModal'));
    // demoModal.show();
});

// Form validation feedback
document.querySelector('form').addEventListener('submit', function(e) {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    
    // Basic client-side validation
    if (!email.value || !email.value.includes('@')) {
        e.preventDefault();
        email.classList.add('is-invalid');
        return;
    }
    
    if (!password.value || password.value.length < 6) {
        e.preventDefault();
        password.classList.add('is-invalid');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Signing In...';
    submitBtn.disabled = true;
});

// Clear validation errors on input
document.getElementById('email').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});

document.getElementById('password').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});
</script>
@endsection

@section('styles')
<style>
/* Custom login page styles */
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
</style>
@endsection
