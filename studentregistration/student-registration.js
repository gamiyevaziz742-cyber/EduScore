// Student Registration JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            if (type === 'password') {
                eyeIcon.className = 'fas fa-eye';
            } else {
                eyeIcon.className = 'fas fa-eye-slash';
            }
        });
    }

    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);

            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            if (type === 'password') {
                eyeIcon.className = 'fas fa-eye';
            } else {
                eyeIcon.className = 'fas fa-eye-slash';
            }
        });
    }

    // Form elements
    const registrationForm = document.getElementById('registrationForm');
    const registerBtn = document.querySelector('.btn-register');
    const termsCheckbox = document.getElementById('terms');

    // Fix for select dropdown placeholder visibility
    // Logic removed in favor of CSS :invalid selector
    const selectElements = document.querySelectorAll('select.custom-select');

    // Real-time validation
    const inputs = document.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function () {
            validateField(this);
        });

        input.addEventListener('input', function () {
            clearError(this);

            // Special handling for password fields
            if (this.name === 'password') {
                checkPasswordStrength(this.value);
            }
            if (this.name === 'confirmPassword') {
                validatePasswordMatch();
            }
        });

        // Handle select change events
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', function () {
                clearError(this);
            });
        }
    });

    // Password strength checker
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('passwordText');

        let strength = 0;
        let text = '';
        let color = '';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        switch (strength) {
            case 0:
            case 1:
                text = 'Weak';
                color = '#ff3b30';
                break;
            case 2:
                text = 'Fair';
                color = '#ff9500';
                break;
            case 3:
                text = 'Good';
                color = '#ffcc00';
                break;
            case 4:
                text = 'Strong';
                color = '#4cd964';
                break;
        }

        strengthBar.style.width = (strength * 25) + '%';
        strengthBar.style.background = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
    }

    // Password match validation
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const errorElement = document.getElementById('confirmPasswordError');

        if (confirmPassword && password !== confirmPassword) {
            showError(confirmPasswordInput, 'Passwords do not match');
            return false;
        } else {
            clearError(confirmPasswordInput);
            return true;
        }
    }

    // Field validation
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';

        switch (fieldName) {
            case 'firstName':
            case 'lastName':
                if (!value) {
                    errorMessage = 'This field is required';
                } else if (!/^[a-zA-Z\s]{2,}$/.test(value)) {
                    errorMessage = 'Please enter a valid name (letters and spaces only)';
                }
                break;

            case 'username':
                if (!value) {
                    errorMessage = 'Username is required';
                } else if (value.length < 3) {
                    errorMessage = 'Username must be at least 3 characters';
                } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                    errorMessage = 'Username can only contain letters, numbers, and underscores';
                }
                break;

            case 'email':
                if (!value) {
                    errorMessage = 'Email is required';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    errorMessage = 'Please enter a valid email address';
                }
                break;

            case 'age':
                if (!value) {
                    errorMessage = 'Age is required';
                } else if (value < 13 || value > 25) {
                    errorMessage = 'Age must be between 13 and 25';
                }
                break;



            case 'school':
                if (!value) {
                    errorMessage = 'School name is required';
                }
                break;

            case 'password':
                if (!value) {
                    errorMessage = 'Password is required';
                } else if (value.length < 8) {
                    errorMessage = 'Password must be at least 8 characters';
                } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) {
                    errorMessage = 'Password must include uppercase, lowercase, and numbers';
                }
                break;

            case 'confirmPassword':
                isValid = validatePasswordMatch();
                break;
        }

        if (errorMessage) {
            showError(field, errorMessage);
            isValid = false;
        }

        return isValid;
    }

    // Validate terms checkbox
    function validateTerms() {
        const errorElement = document.getElementById('termsError');
        if (!termsCheckbox.checked) {
            errorElement.textContent = 'You must agree to the terms and conditions';
            errorElement.classList.add('show');
            return false;
        } else {
            errorElement.classList.remove('show');
            return true;
        }
    }

    // Show error message
    function showError(field, message) {
        const errorElement = document.getElementById(field.name + 'Error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
        field.style.borderColor = 'var(--danger)';
    }

    // Clear error message
    function clearError(field) {
        const errorElement = document.getElementById(field.name + 'Error');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
        field.style.borderColor = '';
    }

    // Form submission
    if (registrationForm) {
        registrationForm.addEventListener('submit', function (e) {
            let isValid = true;

            // Validate all fields
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            // Additional password match validation
            if (!validatePasswordMatch()) {
                isValid = false;
            }

            // Validate terms and conditions
            if (!validateTerms()) {
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault(); // Prevent submission ONLY if invalid
                showAlert('Please fix the errors in the form', 'error');
                // Scroll to first error
                const firstError = document.querySelector('.error-message.show');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                // Allow default submission (no e.preventDefault)
                registerBtn.classList.add('loading');
            }
        });
    }

    // Alert system
    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlert = document.querySelector('.custom-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create alert element
        const alert = document.createElement('div');
        alert.className = `custom-alert alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <i class="fas fa-${getAlertIcon(type)}"></i>
                <span>${message}</span>
                <button class="alert-close">&times;</button>
            </div>
        `;

        // Add styles
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${getAlertColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid ${getAlertBorderColor(type)};
        `;

        document.body.appendChild(alert);

        // Add close functionality
        const closeBtn = alert.querySelector('.alert-close');
        closeBtn.addEventListener('click', () => {
            alert.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    }

    function getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'info': 'info-circle',
            'warning': 'exclamation-triangle'
        };
        return icons[type] || 'info-circle';
    }

    function getAlertColor(type) {
        const colors = {
            'success': '#4cd964',
            'error': '#ff3b30',
            'info': '#007aff',
            'warning': '#ffcc00'
        };
        return colors[type] || '#007aff';
    }

    function getAlertBorderColor(type) {
        const colors = {
            'success': '#2ecc71',
            'error': '#e74c3c',
            'info': '#3498db',
            'warning': '#f39c12'
        };
        return colors[type] || '#3498db';
    }

    // Add CSS animations for alerts
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: auto;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .alert-close:hover {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);
});