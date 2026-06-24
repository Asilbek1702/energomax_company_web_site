/**
 * Energomax Group — main.js
 * Mobile nav toggle. REST quote form handled by plugin's quote-form.js.
 */
(function () {
	'use strict';

	// Mobile burger
	var burger  = document.getElementById('em-burger');
	var mobileNav = document.getElementById('em-mobile-nav');

	if (burger && mobileNav) {
		burger.addEventListener('click', function () {
			var open = mobileNav.classList.toggle('open');
			burger.classList.toggle('open', open);
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	// Smooth scroll for anchor links
	document.querySelectorAll('a[href^="#"]').forEach(function (a) {
		a.addEventListener('click', function (e) {
			var id = a.getAttribute('href').slice(1);
			if (!id) return;
			var target = document.getElementById(id);
			if (target) {
				e.preventDefault();
				target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
	});

	// Active nav link
	var path = window.location.pathname;
	document.querySelectorAll('.em-nav a').forEach(function (a) {
		if (a.getAttribute('href') && path.startsWith(new URL(a.href).pathname) && a.getAttribute('href') !== '/') {
			a.style.color = '#fff';
		}
	});

})();
