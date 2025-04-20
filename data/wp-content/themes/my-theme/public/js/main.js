/**
 * Main JavaScript file for the theme
 * 
 * This file handles all the main JavaScript functionality for the theme.
 * 
 * @package WPFramework
 */

(function() {
    'use strict';

    // DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize mobile menu
        initMobileMenu();
        
        // Initialize smooth scrolling
        initSmoothScroll();
        
        // Initialize Web Components polyfills if needed
        initWebComponentsPolyfills();
    });

    /**
     * Initialize mobile menu functionality
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNavigation = document.querySelector('.main-navigation');
        
        if (!menuToggle || !mainNavigation) return;
        
        menuToggle.addEventListener('click', function() {
            mainNavigation.classList.toggle('toggled');
            
            if (mainNavigation.classList.contains('toggled')) {
                menuToggle.setAttribute('aria-expanded', 'true');
            } else {
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Initialize Web Components polyfills if needed
     */
    function initWebComponentsPolyfills() {
        // Check if Custom Elements are supported
        const supportsCustomElements = 'customElements' in window;
        
        // Check if Shadow DOM is supported
        const supportsShadowDOM = !!HTMLElement.prototype.attachShadow;
        
        // Check if <template> is supported
        const supportsTemplate = 'content' in document.createElement('template');
        
        // If any of these features are not supported, load polyfills
        if (!supportsCustomElements || !supportsShadowDOM || !supportsTemplate) {
            loadScript('/wp-content/themes/wpframework/public/js/polyfills/webcomponents-bundle.js');
        }
        
        // Check if dialog element is supported
        if (!window.HTMLDialogElement) {
            loadScript('/wp-content/themes/wpframework/public/js/polyfills/dialog-polyfill.js');
            loadStylesheet('/wp-content/themes/wpframework/public/css/polyfills/dialog-polyfill.css');
            
            // Initialize dialog polyfill after it's loaded
            window.addEventListener('load', function() {
                if (window.dialogPolyfill) {
                    document.querySelectorAll('dialog').forEach(dialog => {
                        dialogPolyfill.registerDialog(dialog);
                    });
                }
            });
        }
        
        // Check if popover is supported
        if (!HTMLElement.prototype.hasOwnProperty('popover')) {
            loadScript('/wp-content/themes/wpframework/public/js/polyfills/popover-polyfill.js');
        }
    }

    /**
     * Load a script dynamically
     * 
     * @param {string} src Script URL
     */
    function loadScript(src) {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        document.head.appendChild(script);
    }

    /**
     * Load a stylesheet dynamically
     * 
     * @param {string} href Stylesheet URL
     */
    function loadStylesheet(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }
})();
