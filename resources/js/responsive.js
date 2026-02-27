// Responsive utilities for POS System

(function () {
    'use strict';

    // Handle mobile menu toggle
    function initMobileMenu() {
        const mobileToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const sidebarClose = document.getElementById('sidebar-toggle');

        if (mobileToggle && sidebar) {
            mobileToggle.addEventListener('click', function () {
                sidebar.classList.add('mobile-open');
                if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
            });
        }

        if (sidebarClose && sidebar) {
            sidebarClose.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function () {
                if (sidebar) sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.add('hidden');
            });
        }

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && sidebar) {
                sidebar.classList.remove('mobile-open');
                if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
            }
        });
    }

    // Handle responsive tables - no-op, handled per-component with overflow-x-auto now
    function initResponsiveTables() { }

    // Handle responsive modals - no-op, handled per-component now
    function initResponsiveModals() { }

    // Prevent zoom on input focus (iOS)
    function preventZoomOnFocus() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="number"], input[type="email"], input[type="password"], textarea, select');
        inputs.forEach(function (input) {
            if (window.innerWidth <= 640) {
                input.style.fontSize = '16px';
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initMobileMenu();
            initResponsiveTables();
            initResponsiveModals();
            preventZoomOnFocus();
        });
    } else {
        initMobileMenu();
        initResponsiveTables();
        initResponsiveModals();
        preventZoomOnFocus();
    }

    // Re-run on resize
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            initResponsiveTables();
            initResponsiveModals();
            preventZoomOnFocus();
        }, 250);
    });

})();
