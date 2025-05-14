/**
 * Dialog Element Polyfill
 * 
 * This polyfill provides support for the <dialog> element in browsers that don't support it natively.
 * 
 * @package WPFramework
 */

(function(window, document) {
  'use strict';

  // Check if dialog is already supported
  if (window.HTMLDialogElement) {
    console.log('Native dialog support detected');
    return;
  }

  // Dialog polyfill namespace
  var dialogPolyfill = {};

  // The dialog overlay (backdrop)
  var dialogOverlay = document.createElement('div');
  dialogOverlay.className = 'dialog-overlay';
  dialogOverlay.style.position = 'fixed';
  dialogOverlay.style.top = '0';
  dialogOverlay.style.right = '0';
  dialogOverlay.style.bottom = '0';
  dialogOverlay.style.left = '0';
  dialogOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
  dialogOverlay.style.display = 'none';
  dialogOverlay.style.zIndex = '999';
  document.body.appendChild(dialogOverlay);

  // Current active dialog
  var activeDialog = null;

  // Register a dialog with the polyfill
  dialogPolyfill.registerDialog = function(dialog) {
    if (dialog.showModal) {
      console.warn('This dialog is already registered.');
      return;
    }

    // Add default styles to make it behave like a dialog
    dialog.style.position = 'absolute';
    dialog.style.zIndex = '1000';
    dialog.style.display = 'none';

    // Add the open property
    Object.defineProperty(dialog, 'open', {
      get: function() {
        return dialog.hasAttribute('open');
      },
      set: function(value) {
        if (value) {
          dialog.setAttribute('open', '');
        } else {
          dialog.removeAttribute('open');
        }
      },
      configurable: true
    });

    // Add the returnValue property
    dialog.returnValue = '';

    // Add the show method
    dialog.show = function() {
      if (dialog.open) {
        return;
      }
      
      dialog.open = true;
      dialog.style.display = 'block';
      
      // Center the dialog
      centerDialog(dialog);
      
      // Dispatch the show event
      var showEvent = new Event('show', {
        bubbles: false,
        cancelable: true
      });
      dialog.dispatchEvent(showEvent);
    };

    // Add the showModal method
    dialog.showModal = function() {
      if (dialog.open) {
        return;
      }
      
      // Close any currently open modal dialog
      if (activeDialog) {
        activeDialog.close();
      }
      
      dialog.open = true;
      dialog.style.display = 'block';
      
      // Show the overlay
      dialogOverlay.style.display = 'block';
      
      // Center the dialog
      centerDialog(dialog);
      
      // Set as active dialog
      activeDialog = dialog;
      
      // Dispatch the show event
      var showEvent = new Event('show', {
        bubbles: false,
        cancelable: true
      });
      dialog.dispatchEvent(showEvent);
    };

    // Add the close method
    dialog.close = function(returnValue) {
      if (!dialog.open) {
        return;
      }
      
      if (returnValue !== undefined) {
        dialog.returnValue = returnValue;
      }
      
      dialog.open = false;
      dialog.style.display = 'none';
      
      // Hide the overlay if this is the active dialog
      if (activeDialog === dialog) {
        dialogOverlay.style.display = 'none';
        activeDialog = null;
      }
      
      // Dispatch the close event
      var closeEvent = new Event('close', {
        bubbles: false,
        cancelable: true
      });
      dialog.dispatchEvent(closeEvent);
    };

    // Handle ESC key to close modal dialogs
    function handleKeyDown(event) {
      if (event.key === 'Escape' && activeDialog === dialog) {
        event.preventDefault();
        dialog.close();
      }
    }
    document.addEventListener('keydown', handleKeyDown);

    // Handle clicks on the backdrop to close the dialog
    dialogOverlay.addEventListener('click', function(event) {
      if (activeDialog === dialog) {
        dialog.close();
      }
    });

    // Center the dialog in the viewport
    function centerDialog(dialog) {
      var viewportWidth = window.innerWidth;
      var viewportHeight = window.innerHeight;
      var dialogWidth = dialog.offsetWidth;
      var dialogHeight = dialog.offsetHeight;
      
      dialog.style.top = Math.max(0, (viewportHeight - dialogHeight) / 2) + 'px';
      dialog.style.left = Math.max(0, (viewportWidth - dialogWidth) / 2) + 'px';
    }

    // Recenter the dialog on window resize
    window.addEventListener('resize', function() {
      if (dialog.open) {
        centerDialog(dialog);
      }
    });
  };

  // Auto-register all dialog elements
  document.addEventListener('DOMContentLoaded', function() {
    var dialogs = document.querySelectorAll('dialog');
    Array.prototype.forEach.call(dialogs, function(dialog) {
      dialogPolyfill.registerDialog(dialog);
    });
  });

  // Expose the dialogPolyfill to the window
  window.dialogPolyfill = dialogPolyfill;

})(window, document);
