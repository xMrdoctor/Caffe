// Mobile Navigation
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Toggle mobile menu
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking on a link
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });

    // Smooth scrolling for scroll indicator
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            const nextSection = document.querySelector('.features') || document.querySelector('.about-content');
            if (nextSection) {
                nextSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // Header background on scroll
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.background = 'rgba(139, 69, 19, 0.98)';
            } else {
                header.style.background = 'rgba(139, 69, 19, 0.95)';
            }
        });
    }

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.feature-card, .quick-link-card, .contact-card, .menu-item');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// Menu Filtering Functions
let menuItems = [];

// Initialize menu filtering
function initMenuFiltering() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const menuGrid = document.querySelector('.menu-grid');

    if (!filterButtons.length || !menuGrid) return;

    // Store original menu items
    menuItems = Array.from(document.querySelectorAll('.menu-item'));

    // Add click event to filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            // Get filter category
            const category = this.getAttribute('data-category');
            filterMenuItems(category);
        });
    });
}

// Filter menu items by category
function filterMenuItems(category) {
    const menuGrid = document.querySelector('.menu-grid');
    if (!menuGrid) return;

    // Clear current items
    menuGrid.innerHTML = '';

    // Filter items based on category
    let filteredItems = menuItems;
    if (category && category !== 'all') {
        filteredItems = menuItems.filter(item => 
            item.getAttribute('data-category') === category
        );
    }

    // Add filtered items back to grid with animation
    filteredItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            menuGrid.appendChild(item);
            
            // Animate in
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
    });
}

// Admin Panel Functions
function showMessage(message, type = 'success') {
    // Remove existing messages
    const existingMessage = document.querySelector('.admin-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `admin-message ${type}`;
    messageDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 1001;
        animation: slideIn 0.3s ease;
    `;
    messageDiv.textContent = message;

    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(messageDiv);

    // Remove message after 3 seconds
    setTimeout(() => {
        messageDiv.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => messageDiv.remove(), 300);
    }, 3000);
}

// Add menu item (Admin)
function addMenuItem() {
    const form = document.getElementById('addItemForm');
    if (!form) return;

    const formData = new FormData(form);
    
    fetch('admin/add_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('محصول با موفقیت اضافه شد', 'success');
            form.reset();
            loadMenuItems();
        } else {
            showMessage(data.message || 'خطا در افزودن محصول', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('خطا در ارتباط با سرور', 'error');
    });
}

// Delete menu item (Admin)
function deleteMenuItem(id) {
    if (!confirm('آیا از حذف این محصول اطمینان دارید؟')) {
        return;
    }

    fetch('admin/delete_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('محصول با موفقیت حذف شد', 'success');
            loadMenuItems();
        } else {
            showMessage(data.message || 'خطا در حذف محصول', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('خطا در ارتباط با سرور', 'error');
    });
}

// Load menu items (Admin)
function loadMenuItems() {
    const tableBody = document.querySelector('#menuItemsTable tbody');
    if (!tableBody) return;

    fetch('admin/get_items.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            tableBody.innerHTML = '';
            data.items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td>${item.price} تومان</td>
                    <td>
                        <button class="delete-btn" onclick="deleteMenuItem(${item.id})">
                            حذف
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('خطا در بارگذاری محصولات', 'error');
    });
}

// Image preview for admin form
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (!preview) return;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px; border-radius: 10px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<p>پیش‌نمایش تصویر</p>';
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize menu filtering if on menu page
    if (document.querySelector('.menu-content')) {
        initMenuFiltering();
    }

    // Initialize admin functions if on admin page
    if (document.querySelector('.admin-container')) {
        loadMenuItems();
        
        // Add form submit handler
        const addForm = document.getElementById('addItemForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                addMenuItem();
            });
        }

        // Add image preview handler
        const imageInput = document.getElementById('itemImage');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                previewImage(this);
            });
        }
    }
});

// Utility Functions
function formatPrice(price) {
    return new Intl.NumberFormat('fa-IR').format(price);
}

function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            isValid = false;
        } else {
            field.style.borderColor = '#d4a574';
        }
    });

    return isValid;
}

// Lazy loading for images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Call lazy loading on page load
document.addEventListener('DOMContentLoaded', initLazyLoading);

// PWA Service Worker Registration (Optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
        .then(function(registration) {
            console.log('ServiceWorker registration successful');
        })
        .catch(function(err) {
            console.log('ServiceWorker registration failed');
        });
    });
}