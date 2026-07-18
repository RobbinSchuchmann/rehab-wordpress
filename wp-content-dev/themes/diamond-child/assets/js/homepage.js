/**
 * Homepage Redesign — JavaScript
 * Handles Swiper carousels, Fancybox, tabs, accordions, video lightbox, mobile sticky footer.
 * Dependencies: Swiper, Fancybox (loaded from parent theme vendors).
 */
(function () {
	'use strict';

	/* ================================================================
	   1. TABS — generic handler
	   ================================================================ */
	document.querySelectorAll('[data-drt-tabs]').forEach(function (tabGroup) {
		var triggers = tabGroup.querySelectorAll('[data-drt-tab-trigger]');
		var panels   = tabGroup.querySelectorAll('[data-drt-tab-panel]');

		triggers.forEach(function (trigger) {
			trigger.addEventListener('click', function () {
				var target = this.getAttribute('data-drt-tab-trigger');

				// Deactivate all
				triggers.forEach(function (t) {
					t.classList.remove('is-active');
					t.setAttribute('aria-selected', 'false');
				});
				panels.forEach(function (p) {
					p.classList.remove('is-active');
				});

				// Activate clicked
				this.classList.add('is-active');
				this.setAttribute('aria-selected', 'true');

				panels.forEach(function (p) {
					if (p.getAttribute('data-drt-tab-panel') === target) {
						p.classList.add('is-active');
					}
				});

				// Re-initialize Swiper inside newly active panel (treatment mobile)
				var activePanel = tabGroup.querySelector('[data-drt-tab-panel="' + target + '"]');
				if (activePanel) {
					var swiperEl = activePanel.querySelector('[data-drt-swiper="treatment"]');
					if (swiperEl && swiperEl.swiper) {
						swiperEl.swiper.update();
						swiperEl.swiper.slideTo(0, 0);
					}
				}
			});
		});
	});

	/* ================================================================
	   2. ACCORDIONS — generic handler
	   ================================================================ */
	document.querySelectorAll('[data-drt-accordion-trigger]').forEach(function (trigger) {
		trigger.addEventListener('click', function () {
			var parent  = this.closest('[data-drt-accordion]');
			var content = parent ? parent.querySelector('[data-drt-accordion-content]') : this.nextElementSibling;
			if (!content) return;

			var isOpen = content.classList.contains('is-open');

			// Close siblings in same panel (single-open accordion behavior within a tab panel)
			var panel = this.closest('[data-drt-tab-panel]') || this.closest('.drt-accordion');
			if (panel) {
				panel.querySelectorAll('[data-drt-accordion-content].is-open').forEach(function (c) {
					c.classList.remove('is-open');
					var sibTrigger = c.previousElementSibling || (c.closest('[data-drt-accordion]') && c.closest('[data-drt-accordion]').querySelector('[data-drt-accordion-trigger]'));
					if (sibTrigger) sibTrigger.setAttribute('aria-expanded', 'false');
				});
			}

			if (!isOpen) {
				content.classList.add('is-open');
				this.setAttribute('aria-expanded', 'true');
			} else {
				this.setAttribute('aria-expanded', 'false');
			}
		});
	});

	/* ================================================================
	   3. SWIPER CAROUSELS
	   ================================================================ */
	function initSwipers() {
		if (typeof Swiper === 'undefined') return;

		// Gallery carousel (immersive tour)
		document.querySelectorAll('[data-drt-swiper="gallery"]').forEach(function (el) {
			var wrap = el.parentElement;
			new Swiper(el, {
				slidesPerView: 1,
				spaceBetween: 16,
				loop: true,
				navigation: {
					prevEl: wrap.querySelector('.swiper-button-prev'),
					nextEl: wrap.querySelector('.swiper-button-next'),
				},
				pagination: {
					el: el.closest('.drt-tour__gallery') ? el.closest('.drt-tour__gallery').querySelector('.drt-swiper-dots') : null,
					clickable: true,
				},
				breakpoints: {
					768: { slidesPerView: 2 },
					1024: { slidesPerView: 3 },
				},
			});
		});

		// Reviews carousel (autoplay)
		document.querySelectorAll('[data-drt-swiper="reviews"]').forEach(function (el) {
			var wrap = el.parentElement;
			new Swiper(el, {
				slidesPerView: 1,
				spaceBetween: 16,
				loop: true,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false,
					pauseOnMouseEnter: true,
				},
				navigation: {
					prevEl: wrap.querySelector('.swiper-button-prev'),
					nextEl: wrap.querySelector('.swiper-button-next'),
				},
				pagination: {
					el: el.closest('.drt-testimonials__reviews-carousel') ? el.closest('.drt-testimonials__reviews-carousel').querySelector('.drt-swiper-dots') : null,
					clickable: true,
				},
				breakpoints: {
					768: { slidesPerView: 2 },
					1024: { slidesPerView: 3 },
				},
			});
		});

		// Team carousel (autoplay, 5 slides desktop)
		document.querySelectorAll('[data-drt-swiper="team"]').forEach(function (el) {
			var wrap = el.parentElement;
			new Swiper(el, {
				slidesPerView: 2,
				spaceBetween: 8,
				loop: true,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false,
					pauseOnMouseEnter: true,
				},
				navigation: {
					prevEl: wrap.querySelector('.swiper-button-prev'),
					nextEl: wrap.querySelector('.swiper-button-next'),
				},
				pagination: {
					el: el.closest('.drt-team__carousel') ? el.closest('.drt-team__carousel').querySelector('.drt-swiper-dots') : null,
					clickable: true,
				},
				breakpoints: {
					640: { slidesPerView: 3, spaceBetween: 8 },
					768: { slidesPerView: 4, spaceBetween: 8 },
					1024: { slidesPerView: 5, spaceBetween: 8 },
				},
			});
		});

		// Treatment mobile carousel (1 slide)
		document.querySelectorAll('[data-drt-swiper="treatment"]').forEach(function (el) {
			new Swiper(el, {
				slidesPerView: 1,
				spaceBetween: 16,
				loop: true,
				pagination: {
					el: el.querySelector('.drt-swiper-dots'),
					clickable: true,
				},
			});
		});

		// Video testimonials mobile carousel
		document.querySelectorAll('[data-drt-swiper="videos"]').forEach(function (el) {
			new Swiper(el, {
				slidesPerView: 1,
				spaceBetween: 16,
				loop: true,
				centeredSlides: true,
				pagination: {
					el: el.querySelector('.drt-swiper-dots'),
					clickable: true,
				},
			});
		});
	}

	/* ================================================================
	   4. FANCYBOX — gallery lightbox
	   ================================================================ */
	function initFancybox() {
		if (typeof Fancybox === 'undefined') return;

		Fancybox.bind('[data-fancybox="gallery"]', {
			Thumbs: false,
			// Fancybox v5 wants an object of {left,middle,right} arrays here — a
			// bare array throws during render and leaves the lightbox empty
			// (REH-145).
			Toolbar: {
				display: { left: [], middle: [], right: ['close'] },
			},
		});

		// Inline modal (CTA → #modal-free-assessment)
		Fancybox.bind('.fancybox[data-type="inline"]', {
			type: 'inline',
			groupAll: false,
		});
	}

	/* ================================================================
	   5. VIDEO LIGHTBOX — YouTube embeds via Fancybox
	   ================================================================ */
	function initVideoLightbox() {
		document.querySelectorAll('[data-drt-video]').forEach(function (el) {
			el.style.cursor = 'pointer';
			el.addEventListener('click', function (e) {
				e.preventDefault();
				var videoId = this.getAttribute('data-drt-video');
				if (!videoId) return;

				// Check if this is a portrait video (9:16 thumbnail)
				var isPortrait = !!this.closest('.drt-testimonials__video-thumb');
				var iframeWidth = isPortrait ? 405 : 960;
				var iframeHeight = isPortrait ? 720 : 540;

				if (typeof Fancybox !== 'undefined') {
					Fancybox.show([{
						src: 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1',
						type: 'iframe',
						preload: false,
						width: iframeWidth,
						height: iframeHeight,
					}], {
						groupAll: false,
					});
				} else {
					window.open('https://www.youtube.com/watch?v=' + videoId, '_blank');
				}
			});
		});
	}

	/* ================================================================
	   6. REVIEW CARD — Read more / Show less toggle
	   ================================================================ */
	function initReviewCards() {
		// Use event delegation to handle Swiper loop cloned slides
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-review-toggle]');
			if (!btn) return;
			e.stopPropagation();
			var card = btn.closest('[data-review-card]');
			if (!card) return;

			var isExpanded = card.classList.toggle('is-expanded');
			btn.textContent = isExpanded ? 'Show less' : 'Read more';
		});
	}

	/* ================================================================
	   7. MOBILE STICKY FOOTER — show after 250px scroll, only < 640px
	   ================================================================ */
	function initMobileStickyFooter() {
		var sticky = document.getElementById('drt-mobile-sticky');
		if (!sticky) return;

		var visible = false;

		function checkScroll() {
			var shouldShow = window.scrollY > 250 && window.innerWidth < 640;

			if (shouldShow && !visible) {
				sticky.removeAttribute('hidden');
				visible = true;
			} else if (!shouldShow && visible) {
				sticky.setAttribute('hidden', '');
				visible = false;
			}
		}

		window.addEventListener('scroll', checkScroll, { passive: true });
		window.addEventListener('resize', checkScroll, { passive: true });
		checkScroll();
	}

	/* ================================================================
	   8. INIT — run everything on DOMContentLoaded
	   ================================================================ */
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	function init() {
		// Fancybox + delegated handlers bind first and each step is isolated, so a
		// throw inside one carousel's Swiper init can't stop the gallery lightbox
		// from binding (the cause of "clicking a gallery image does nothing" — REH-44).
		[ initFancybox, initVideoLightbox, initReviewCards, initSwipers, initMobileStickyFooter ].forEach( function ( fn ) {
			try {
				fn();
			} catch ( e ) {
				if ( window.console && window.console.error ) {
					window.console.error( '[homepage] init step failed:', e );
				}
			}
		} );
	}

})();
