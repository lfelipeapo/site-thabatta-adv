/**
 * Web Components polyfill bundle
 * 
 * This is a minimal polyfill bundle for Web Components support in older browsers.
 * It includes Custom Elements, Shadow DOM, and Template polyfills.
 * 
 * @package WPFramework
 */

(function() {
  'use strict';

  // Custom Elements polyfill
  if (!('customElements' in window)) {
    window.customElements = {
      define: function(name, constructor) {
        constructor.prototype.connectedCallback = constructor.prototype.connectedCallback || function() {};
        constructor.prototype.disconnectedCallback = constructor.prototype.disconnectedCallback || function() {};
        constructor.prototype.attributeChangedCallback = constructor.prototype.attributeChangedCallback || function() {};
        constructor.prototype.adoptedCallback = constructor.prototype.adoptedCallback || function() {};
        
        document.createElement(name).prototype = constructor.prototype;
        
        document.addEventListener('DOMContentLoaded', function() {
          Array.from(document.getElementsByTagName(name)).forEach(function(element) {
            Object.setPrototypeOf(element, constructor.prototype);
            element.connectedCallback();
          });
        });
        
        const originalCreateElement = document.createElement;
        document.createElement = function(tagName) {
          const element = originalCreateElement.call(document, tagName);
          if (tagName.toLowerCase() === name) {
            Object.setPrototypeOf(element, constructor.prototype);
          }
          return element;
        };
      },
      get: function(name) {
        return null;
      }
    };
  }

  // Shadow DOM polyfill
  if (!HTMLElement.prototype.attachShadow) {
    HTMLElement.prototype.attachShadow = function(options) {
      const shadowRoot = document.createElement('div');
      shadowRoot.className = 'shadow-root';
      shadowRoot.host = this;
      this.shadowRoot = shadowRoot;
      this.appendChild(shadowRoot);
      return shadowRoot;
    };
  }

  // Template polyfill
  if (!('content' in document.createElement('template'))) {
    Object.defineProperty(HTMLTemplateElement.prototype, 'content', {
      get: function() {
        const fragment = document.createDocumentFragment();
        Array.from(this.childNodes).forEach(function(node) {
          fragment.appendChild(node.cloneNode(true));
        });
        return fragment;
      }
    });
  }

  // Slot polyfill
  if (!customElements.get('slot')) {
    class SlotPolyfill extends HTMLElement {
      connectedCallback() {
        const name = this.getAttribute('name');
        const parent = this.parentNode;
        
        if (parent && parent.host) {
          const children = Array.from(parent.host.children);
          children.forEach(child => {
            if (name && child.getAttribute('slot') === name) {
              this.appendChild(child.cloneNode(true));
            } else if (!name && !child.hasAttribute('slot')) {
              this.appendChild(child.cloneNode(true));
            }
          });
        }
      }
    }
    
    customElements.define('slot', SlotPolyfill);
  }

  console.log('Web Components polyfills loaded');
})();
