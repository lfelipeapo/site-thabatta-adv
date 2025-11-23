/**
 * Popover Element Polyfill
 * 
 * This polyfill provides support for the popover attribute in browsers that don't support it natively.
 * 
 * @package WPFramework
 */

(function(window, document) {
  'use strict';

  // Check if popover is already supported
  if (HTMLElement.prototype.hasOwnProperty('popover')) {
    console.log('Native popover support detected');
    return;
  }

  console.log('Popover polyfill loaded');

  // Current active popover
  let activePopover = null;

  // Add popover functionality to all elements with popover attribute
  function initializePopovers() {
    const popovers = document.querySelectorAll('[popover]');
    
    popovers.forEach(popover => {
      // Skip if already initialized
      if (popover._popoverInitialized) return;
      
      // Mark as initialized
      popover._popoverInitialized = true;
      
      // Set initial styles
      popover.style.display = 'none';
      popover.style.position = 'absolute';
      popover.style.zIndex = '1000';
      popover.style.backgroundColor = '#fff';
      popover.style.borderRadius = '4px';
      popover.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
      popover.style.padding = '10px';
      popover.style.maxWidth = '300px';
      
      // Add methods
      popover.showPopover = function() {
        // Close any open popover
        if (activePopover && activePopover !== this) {
          activePopover.hidePopover();
        }
        
        // Show this popover
        this.style.display = 'block';
        activePopover = this;
        
        // Position the popover
        positionPopover(this);
        
        // Dispatch show event
        const showEvent = new Event('toggle', {
          bubbles: true
        });
        this.dispatchEvent(showEvent);
      };
      
      popover.hidePopover = function() {
        this.style.display = 'none';
        
        if (activePopover === this) {
          activePopover = null;
        }
        
        // Dispatch hide event
        const hideEvent = new Event('toggle', {
          bubbles: true
        });
        this.dispatchEvent(hideEvent);
      };
      
      popover.togglePopover = function() {
        if (this.style.display === 'none') {
          this.showPopover();
        } else {
          this.hidePopover();
        }
      };
      
      // Find and set up trigger elements
      const popoverId = popover.id;
      if (popoverId) {
        const triggers = document.querySelectorAll(`[popovertarget="${popoverId}"]`);
        
        triggers.forEach(trigger => {
          trigger.addEventListener('click', function(e) {
            e.preventDefault();
            popover.togglePopover();
          });
        });
      }
    });
  }

  // Position the popover relative to its trigger
  function positionPopover(popover) {
    const popoverId = popover.id;
    if (!popoverId) return;
    
    const trigger = document.querySelector(`[popovertarget="${popoverId}"]`);
    if (!trigger) return;
    
    const triggerRect = trigger.getBoundingClientRect();
    const popoverRect = popover.getBoundingClientRect();
    
    // Default position (bottom)
    let top = triggerRect.bottom + window.scrollY + 10;
    let left = triggerRect.left + window.scrollX + (triggerRect.width / 2) - (popoverRect.width / 2);
    
    // Check if popover would go off-screen
    if (left < 10) left = 10;
    if (left + popoverRect.width > window.innerWidth - 10) {
      left = window.innerWidth - popoverRect.width - 10;
    }
    
    // Apply position
    popover.style.top = `${top}px`;
    popover.style.left = `${left}px`;
  }

  // Close popover when clicking outside
  document.addEventListener('click', function(e) {
    if (!activePopover) return;
    
    // Check if click is on the active popover or its trigger
    const popoverId = activePopover.id;
    const trigger = popoverId ? document.querySelector(`[popovertarget="${popoverId}"]`) : null;
    
    if (e.target !== activePopover && 
        !activePopover.contains(e.target) && 
        e.target !== trigger && 
        (!trigger || !trigger.contains(e.target))) {
      activePopover.hidePopover();
    }
  });

  // Close popover on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && activePopover) {
      activePopover.hidePopover();
    }
  });

  // Initialize on DOM ready
  document.addEventListener('DOMContentLoaded', initializePopovers);
  
  // Re-initialize when content changes (for dynamically added popovers)
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.addedNodes.length) {
        initializePopovers();
      }
    });
  });
  
  observer.observe(document.body, { childList: true, subtree: true });

  // Add popover property to HTMLElement prototype
  Object.defineProperty(HTMLElement.prototype, 'popover', {
    get: function() {
      return this.getAttribute('popover');
    },
    set: function(value) {
      if (value === null) {
        this.removeAttribute('popover');
      } else {
        this.setAttribute('popover', value);
        
        // Initialize if not already
        if (!this._popoverInitialized) {
          initializePopovers();
        }
      }
    }
  });

})(window, document);
