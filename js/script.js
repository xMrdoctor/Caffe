document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger-menu');
    const mobileMenu = document.getElementById('mobile-menu-links');
    const body = document.body;
    let isMenuOpen = false;

    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', function() {
            isMenuOpen = !isMenuOpen;
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');

            // Prevent body scrolling when the mobile menu is open
            if (isMenuOpen) {
                // Save current scroll position
                const scrollY = window.scrollY;
                body.style.overflow = 'hidden';
                // The following line is to prevent the page from jumping on some browsers
                body.style.position = 'fixed';
                body.style.top = `-${scrollY}px`;
                body.style.width = '100%';
            } else {
                // Restore scroll position
                const scrollY = body.style.top;
                body.style.overflow = 'auto';
                body.style.position = '';
                body.style.top = '';
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        });
    }
});
