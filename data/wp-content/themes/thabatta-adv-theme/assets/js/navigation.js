/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
document.addEventListener('DOMContentLoaded', () => {
	// Responsive Nav Toggle
	const siteNavigation = document.getElementById('site-navigation');
	
	// Return early if the navigation doesn't exist.
	if (!siteNavigation) {
		return;
	}
	
	const menuToggle = siteNavigation.querySelector('.menu-toggle');
	const navMenu = siteNavigation.querySelector('ul');
	
	// Return early if the button or menu doesn't exist.
	if (!menuToggle || !navMenu) {
		return;
	}
	
	// Add accessibility attributes
	menuToggle.setAttribute('aria-controls', navMenu.getAttribute('id'));
	menuToggle.setAttribute('aria-expanded', 'false');
	
	// Hide menu toggle button if menu is empty and return early.
	if (!navMenu.childNodes.length) {
		menuToggle.style.display = 'none';
		return;
	}
	
	if (!navMenu.classList.contains('nav-menu')) {
		navMenu.classList.add('nav-menu');
	}
	
	// Toggle the .active class and the aria-expanded value each time the button is clicked.
	menuToggle.addEventListener('click', () => {
		navMenu.classList.toggle('active');
		
		if (menuToggle.getAttribute('aria-expanded') === 'true') {
			menuToggle.setAttribute('aria-expanded', 'false');
		} else {
			menuToggle.setAttribute('aria-expanded', 'true');
		}
	});
	
	// Remove the .active class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener('click', (event) => {
		const isClickInside = siteNavigation.contains(event.target);
		
		if (!isClickInside) {
			navMenu.classList.remove('active');
			menuToggle.setAttribute('aria-expanded', 'false');
		}
	});
	
	// Smooth Scroll for Anchor Links
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function (e) {
			const href = this.getAttribute('href');
			
			// Only apply smooth scroll for same-page links
			if (href.charAt(0) === '#') {
				e.preventDefault();
				const target = document.querySelector(href);
				
				if (target) {
					// Close menu in mobile if it's open
					if (window.innerWidth < 768) {
						navMenu.classList.remove('active');
						menuToggle.setAttribute('aria-expanded', 'false');
					}
					
					target.scrollIntoView({ behavior: 'smooth' });
				}
			}
		});
	});
	
	// Get all the link elements with children within the menu.
	const linksWithChildren = navMenu.querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a');
	
	// Toggle focus for touchscreen devices
	for (const link of linksWithChildren) {
		link.addEventListener('touchstart', toggleFocus, false);
	}
	
	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if (event.type === 'touchstart') {
			const menuItem = this.parentNode;
			event.preventDefault();
			for (const link of menuItem.parentNode.children) {
				if (menuItem !== link) {
					link.classList.remove('focus');
				}
			}
			menuItem.classList.toggle('focus');
		}
	}
}); 