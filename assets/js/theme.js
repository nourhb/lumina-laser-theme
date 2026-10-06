/**
 * Lumina theme interactions.
 * Vanilla JS: scroll reveals, animated counters, back-to-top.
 * All motion is disabled when the user prefers reduced motion.
 */
(function () {
	'use strict';

	var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Scroll reveal */
	var revealEls = document.querySelectorAll('.lumina-reveal');
	if (revealEls.length && !prefersReduced && 'IntersectionObserver' in window) {
		var revealObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					revealObserver.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12 });
		revealEls.forEach(function (el) { revealObserver.observe(el); });
	} else {
		revealEls.forEach(function (el) { el.classList.add('is-visible'); });
	}

	/* Animated counters */
	function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

	function animateCount(el) {
		var target = parseInt(el.getAttribute('data-count'), 10);
		if (isNaN(target)) { return; }
		if (prefersReduced) { el.textContent = target.toLocaleString(); return; }
		var duration = 1600;
		var start = null;
		function step(timestamp) {
			if (!start) { start = timestamp; }
			var progress = Math.min((timestamp - start) / duration, 1);
			var value = Math.round(target * easeOut(progress));
			el.textContent = value.toLocaleString();
			if (progress < 1) { window.requestAnimationFrame(step); }
		}
		window.requestAnimationFrame(step);
	}

	var counters = document.querySelectorAll('.lumina-count [data-count]');
	if (counters.length && 'IntersectionObserver' in window) {
		var countObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					animateCount(entry.target);
					countObserver.unobserve(entry.target);
				}
			});
		}, { threshold: 0.4 });
		counters.forEach(function (el) { countObserver.observe(el); });
	}

	/* Back to top */
	var topBtn = document.createElement('button');
	topBtn.className = 'lumina-top';
	topBtn.setAttribute('aria-label', 'Back to top');
	topBtn.innerHTML = '&uarr;';
	document.body.appendChild(topBtn);

	function toggleTop() {
		if (window.scrollY > 600) {
			topBtn.classList.add('is-visible');
		} else {
			topBtn.classList.remove('is-visible');
		}
	}
	window.addEventListener('scroll', toggleTop, { passive: true });
	toggleTop();

	topBtn.addEventListener('click', function () {
		window.scrollTo({ top: 0, behavior: prefersReduced ? 'auto' : 'smooth' });
	});
})();
