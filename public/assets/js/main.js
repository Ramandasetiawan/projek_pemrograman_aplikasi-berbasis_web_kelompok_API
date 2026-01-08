
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {

    initTooltips();

    initFormValidations();

    initCartFunctions();

    initWishlistFunctions();

    initSearch();

    initLazyLoading();

    initSmoothScroll();

    initNotifications();

    console.log('🚀 etectstore initialized successfully!');
}

function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initFormValidations() {

    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateEmail(this);
        });
    });

    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });
    });

    const passwordInputs = document.querySelectorAll('input[type="password"][name="new_password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            showPasswordStrength(this);
        });
    });

    const confirmPasswordInputs = document.querySelectorAll('input[name="confirm_password"]');
    confirmPasswordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const passwordInput = document.querySelector('input[name="new_password"]');
            if (passwordInput && this.value !== passwordInput.value) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
    });
}

function validateEmail(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (input.value && !emailRegex.test(input.value)) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else if (input.value) {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    }
}

function showPasswordStrength(input) {
    const password = input.value;
    let strength = 0;

    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;

    let indicator = input.parentElement.querySelector('.password-strength');
    if (indicator) {
        indicator.remove();
    }

    if (password.length > 0) {
        indicator = document.createElement('div');
        indicator.className = 'password-strength mt-1';

        const colors = ['danger', 'warning', 'info', 'success', 'success'];
        const texts = ['Lemah', 'Cukup', 'Sedang', 'Kuat', 'Sangat Kuat'];

        indicator.innerHTML = `<small class="text-${colors[strength - 1]}">
            <i class="bi bi-shield-${strength > 2 ? 'check' : 'exclamation'}"></i> 
            Password ${texts[strength - 1]}
        </small>`;

        input.parentElement.appendChild(indicator);
    }
}

function initCartFunctions() {

    const addToCartForms = document.querySelectorAll('form[action*="cart_add"]');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const productName = this.closest('.card').querySelector('.card-title')?.textContent || 'Produk';

            const button = this.querySelector('button[type="submit"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menambah...';
                button.disabled = true;

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 1500);
            }
        });
    });

    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            let value = parseInt(this.value) || min;

            if (value < min) value = min;
            if (value > max) value = max;

            this.value = value;
        });
    });
}

function initWishlistFunctions() {
    const wishlistButtons = document.querySelectorAll('button[data-wishlist]');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const isAdded = this.classList.contains('active');

            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-heart');
                icon.classList.toggle('bi-heart-fill');
            }

            this.classList.toggle('active');

            showNotification(
                isAdded ? 'Dihapus dari wishlist' : 'Ditambahkan ke wishlist',
                isAdded ? 'warning' : 'success'
            );
        });
    });
}

function initSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);

            const searchButton = this.closest('form').querySelector('button[type="submit"]');
            if (searchButton && this.value.length > 0) {
                searchButton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            }

            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3) {

                    console.log('Searching for:', this.value);
                }

                if (searchButton) {
                    searchButton.innerHTML = '<i class="bi bi-search"></i> Cari';
                }
            }, 500);
        });
    }
}

function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function initNotifications() {

    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function confirmDelete(message = 'Apakah Anda yakin ingin menghapus ini?') {
    return confirm(message);
}

function formatCurrency(amount) {
    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
}

function calculateCartTotal() {
    const cartItems = document.querySelectorAll('.cart-item');
    let total = 0;

    cartItems.forEach(item => {
        const price = parseFloat(item.dataset.price);
        const quantity = parseInt(item.querySelector('input[name="quantity"]')?.value || 1);
        total += price * quantity;
    });

    const totalElement = document.querySelector('#cart-total');
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }

    return total;
}

function showProductQuickView(productId) {

    console.log('Quick view for product:', productId);
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.querySelector('#image-preview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Teks berhasil disalin!', 'success');
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

window.addEventListener('scroll', function() {
    const scrollButton = document.querySelector('#scroll-to-top');
    if (scrollButton) {
        if (window.pageYOffset > 300) {
            scrollButton.style.display = 'block';
        } else {
            scrollButton.style.display = 'none';
        }
    }
});

function printPage() {
    window.print();
}

function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        }).then(() => {
            showNotification('Berhasil dibagikan!', 'success');
        }).catch(console.error);
    } else {
        copyToClipboard(window.location.href);
        showNotification('Link telah disalin!', 'info');
    }
}

window.etectstore = {
    showNotification,
    confirmDelete,
    formatCurrency,
    calculateCartTotal,
    showProductQuickView,
    previewImage,
    copyToClipboard,
    scrollToTop,
    printPage,
    sharePage
};
