<!-- Floating Action Buttons -->
<div class="floating-actions">
    <!-- Main FAB -->
    <div class="fab-main" id="fabMain">
        <i class="bi bi-plus"></i>
    </div>
    
    <!-- Sub FABs -->
    <div class="fab-options" id="fabOptions">
        <div class="fab-option" data-bs-toggle="modal" data-bs-target="#betSlipModal" title="Bet Slip">
            <i class="bi bi-ticket-perforated"></i>
        </div>
        <div class="fab-option" data-bs-toggle="modal" data-bs-target="#depositModal" title="Deposit">
            <i class="bi bi-wallet2"></i>
        </div>
        <div class="fab-option" data-bs-toggle="modal" data-bs-target="#liveChatModal" title="Live Chat">
            <i class="bi bi-chat-dots"></i>
        </div>
        <div class="fab-option" data-bs-toggle="modal" data-bs-target="#notificationsModal" title="Notifications">
            <i class="bi bi-bell"></i>
        </div>
        <div class="fab-option" data-bs-toggle="modal" data-bs-target="#contactModal" title="Contact Us">
            <i class="bi bi-envelope"></i>
        </div>
    </div>
</div>

<style>
/* Floating Action Button Styles */
.floating-actions {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
}

.fab-main {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    font-size: 1.5rem;
    position: relative;
    z-index: 1001;
}

.fab-main:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
}

.fab-main.active {
    transform: rotate(45deg);
}

.fab-options {
    position: absolute;
    bottom: 70px;
    right: 0;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.fab-options.show {
    opacity: 1;
    visibility: visible;
}

.fab-option {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    margin-bottom: 1rem;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    font-size: 1.2rem;
    transform: scale(0);
    position: relative;
}

.fab-options.show .fab-option {
    transform: scale(1);
}

.fab-options.show .fab-option:nth-child(1) { transition-delay: 0.1s; }
.fab-options.show .fab-option:nth-child(2) { transition-delay: 0.2s; }
.fab-options.show .fab-option:nth-child(3) { transition-delay: 0.3s; }
.fab-options.show .fab-option:nth-child(4) { transition-delay: 0.4s; }
.fab-options.show .fab-option:nth-child(5) { transition-delay: 0.5s; }

.fab-option:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.fab-option::before {
    content: attr(title);
    position: absolute;
    right: 60px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.875rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    pointer-events: none;
}

.fab-option:hover::before {
    opacity: 1;
    visibility: visible;
    transform: translateY(-50%) translateX(-5px);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .floating-actions {
        bottom: 1rem;
        right: 1rem;
    }
    
    .fab-main {
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }
    
    .fab-option {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
    }
    
    .fab-options {
        bottom: 60px;
    }
}

/* Hide on very small screens to avoid interference */
@media (max-width: 480px) {
    .floating-actions {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fabMain = document.getElementById('fabMain');
    const fabOptions = document.getElementById('fabOptions');
    
    if (fabMain && fabOptions) {
        let isOpen = false;
        
        fabMain.addEventListener('click', function() {
            if (isOpen) {
                // Close
                fabOptions.classList.remove('show');
                fabMain.classList.remove('active');
                isOpen = false;
            } else {
                // Open
                fabOptions.classList.add('show');
                fabMain.classList.add('active');
                isOpen = true;
            }
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (isOpen && !e.target.closest('.floating-actions')) {
                fabOptions.classList.remove('show');
                fabMain.classList.remove('active');
                isOpen = false;
            }
        });
        
        // Close when option is clicked
        document.querySelectorAll('.fab-option').forEach(option => {
            option.addEventListener('click', function() {
                fabOptions.classList.remove('show');
                fabMain.classList.remove('active');
                isOpen = false;
            });
        });
    }
});
</script>
