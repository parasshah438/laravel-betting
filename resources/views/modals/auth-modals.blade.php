<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="loginModalLabel">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Welcome Back
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <img src="https://via.placeholder.com/80x80/6f42c1/ffffff?text=BP" alt="BetMaster Pro" class="rounded-circle mb-2">
                    <p class="text-secondary mb-0">Sign in to your account</p>
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="modalEmail" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" 
                                   class="form-control border-start-0 ps-0" 
                                   id="modalEmail" 
                                   name="email" 
                                   placeholder="Enter your email"
                                   required>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="modalPassword" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" 
                                   class="form-control border-start-0 border-end-0 ps-0" 
                                   id="modalPassword" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                            <button class="btn btn-outline-secondary border-start-0" type="button" id="toggleModalPassword">
                                <i class="bi bi-eye" id="toggleModalPasswordIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="modalRemember">
                                <label class="form-check-label small" for="modalRemember">
                                    Remember me
                                </label>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <a href="#" class="text-primary text-decoration-none small" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">
                                Forgot Password?
                            </a>
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
                            <hr class="text-muted">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OR</span>
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="row g-2">
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
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <div class="w-100 text-center">
                    <p class="mb-0 small">Don't have an account? 
                        <a href="#" class="text-primary fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">
                            Sign Up
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white border-0">
                <h5 class="modal-title fw-bold" id="registerModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Join BetMaster Pro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <img src="https://via.placeholder.com/80x80/28a745/ffffff?text=BP" alt="BetMaster Pro" class="rounded-circle mb-2">
                    <p class="text-secondary mb-0">Create your betting account</p>
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-gift me-1"></i>Welcome Bonus: $200
                        </span>
                    </div>
                </div>

                <form id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="row">
                        <!-- Full Name -->
                        <div class="col-md-6 mb-3">
                            <label for="modalRegisterName" class="form-label fw-semibold">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 ps-0" 
                                       id="modalRegisterName" 
                                       name="name" 
                                       placeholder="Your full name"
                                       required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="modalRegisterEmail" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" 
                                       class="form-control border-start-0 ps-0" 
                                       id="modalRegisterEmail" 
                                       name="email" 
                                       placeholder="your@email.com"
                                       required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="modalRegisterPhone" class="form-label fw-semibold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-telephone text-muted"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control border-start-0 ps-0" 
                                       id="modalRegisterPhone" 
                                       name="phone" 
                                       placeholder="+1 (555) 123-4567"
                                       required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6 mb-3">
                            <label for="modalRegisterDOB" class="form-label fw-semibold">Date of Birth</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar text-muted"></i>
                                </span>
                                <input type="date" 
                                       class="form-control border-start-0 ps-0" 
                                       id="modalRegisterDOB" 
                                       name="date_of_birth" 
                                       required>
                            </div>
                            <small class="text-muted">You must be 18+ to register</small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label for="modalRegisterPassword" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 border-end-0 ps-0" 
                                       id="modalRegisterPassword" 
                                       name="password" 
                                       placeholder="Create password"
                                       required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" id="toggleRegisterPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-1" id="passwordStrength"></div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 mb-3">
                            <label for="modalRegisterPasswordConfirm" class="form-label fw-semibold">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 ps-0" 
                                       id="modalRegisterPasswordConfirm" 
                                       name="password_confirmation" 
                                       placeholder="Confirm password"
                                       required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="terms" id="modalTerms" required>
                            <label class="form-check-label small" for="modalTerms">
                                I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modalNewsletter">
                            <label class="form-check-label small" for="modalNewsletter">
                                Send me promotional offers and updates
                            </label>
                        </div>
                    </div>

                    <!-- Register Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </div>

                    <!-- Social Register -->
                    <div class="text-center mb-2">
                        <small class="text-muted">Or register with:</small>
                    </div>
                    <div class="row g-2">
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
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <div class="w-100 text-center">
                    <p class="mb-0 small">Already have an account? 
                        <a href="#" class="text-primary fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
                            Sign In
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-warning text-dark border-0">
                <h5 class="modal-title fw-bold" id="forgotPasswordModalLabel">
                    <i class="bi bi-key me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill text-warning" style="font-size: 3rem;"></i>
                    <p class="text-secondary mt-3 mb-0">Enter your email address and we'll send you a link to reset your password.</p>
                </div>

                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="modalForgotEmail" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" 
                                   class="form-control border-start-0 ps-0" 
                                   id="modalForgotEmail" 
                                   name="email" 
                                   placeholder="Enter your email address"
                                   required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning btn-lg text-dark">
                            <i class="bi bi-send me-2"></i>Send Reset Link
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <a href="#" class="text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Us Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-info text-white border-0">
                <h5 class="modal-title fw-bold" id="contactModalLabel">
                    <i class="bi bi-chat-dots me-2"></i>Contact Us
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contactName" class="form-label fw-semibold">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-person text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control border-start-0 ps-0" 
                                               id="contactName" 
                                               name="name" 
                                               placeholder="Your name"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contactEmail" class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-envelope text-muted"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control border-start-0 ps-0" 
                                               id="contactEmail" 
                                               name="email" 
                                               placeholder="your@email.com"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contactPhone" class="form-label fw-semibold">Phone (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-telephone text-muted"></i>
                                        </span>
                                        <input type="tel" 
                                               class="form-control border-start-0 ps-0" 
                                               id="contactPhone" 
                                               name="phone" 
                                               placeholder="Phone number">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contactSubject" class="form-label fw-semibold">Subject</label>
                                    <select class="form-select" id="contactSubject" name="subject" required>
                                        <option value="">Select a topic</option>
                                        <option value="general">General Inquiry</option>
                                        <option value="account">Account Issue</option>
                                        <option value="betting">Betting Question</option>
                                        <option value="payment">Payment Issue</option>
                                        <option value="technical">Technical Support</option>
                                        <option value="complaint">Complaint</option>
                                        <option value="suggestion">Suggestion</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="contactMessage" class="form-label fw-semibold">Message</label>
                                <textarea class="form-control" 
                                          id="contactMessage" 
                                          name="message" 
                                          rows="4" 
                                          placeholder="Tell us how we can help you..."
                                          required></textarea>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <span id="messageCount">0</span>/500 characters
                                    </small>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-info btn-lg text-white">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>Contact Information
                            </h6>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <strong class="small">Support Hours</strong>
                                </div>
                                <p class="small text-muted mb-0">24/7 Live Support</p>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    <strong class="small">Phone</strong>
                                </div>
                                <p class="small text-muted mb-0">+1 (888) 123-4567</p>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    <strong class="small">Email</strong>
                                </div>
                                <p class="small text-muted mb-0">support@betmaster.com</p>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-chat-dots text-primary me-2"></i>
                                    <strong class="small">Live Chat</strong>
                                </div>
                                <button class="btn btn-outline-primary btn-sm w-100">
                                    Start Live Chat
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <p class="small text-muted mb-2">Follow us on:</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
