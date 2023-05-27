(function ($) {
	"use strict";
	var G5PlusTestimonials = {
		init: function () {
			if ($.isFunction($.fn.owlCarousel)) {
				$('.testimonials-style-03').each(function () {
					G5PlusTestimonials.init_slider_nav($(this));
				});
			}
		},
		init_slider_nav: function ($elm) {
			var isRTL = $('body').hasClass('rtl'),
				sync1 = $('.testimonials-quoter-slider', $elm),
				sync2 = $('.testimonials-avatar-slider', $elm),
				$item_active = sync2.attr('data-item-active'),
				$owl_options = sync2.attr('data-owl-options'),
				option = JSON.parse($owl_options);
			
			/*Initialized Sync Slider*/
			sync1.owlCarousel({
				items: 1,
				slideSpeed: 2000,
				nav: false,
				center: true,
				dots: option['dots'],
				autoPlay: true,
				responsiveRefreshRate: 200,
				autoHeight: true,
				rtl: isRTL
			}).on('changed.owl.carousel', syncPosition);

			sync2.on('initialized.owl.carousel', function () {
				sync2.find(".owl-item").eq(0).addClass("current");
			}).owlCarousel({
				items: option['items'],
				nav: option['nav'],
				dots: false,
				autoPlay: option['autoplay'],
				smartSpeed: 500,
				slideSpeed: 1000,
				center: true,
				responsiveRefreshRate: 100,
				navText: option['navText'],
				mouseDrag: false,
				rtl: isRTL,
				responsive: {
					0: {
						items: 1,
						nav: false
					},
					480: {
						items: $item_active,
						nav: false
					},
					768: {
						items: $item_active,
						nav: option['nav']
					}
				}
			}).on('changed.owl.carousel', syncPosition2);
			function syncPosition(el) {
				var index = el.item.index;
                sync2.find('.owl-item').removeClass('current').eq(index).addClass("current");
                sync2.data('owl.carousel').to(index, 500, true);
			}
			
			function syncPosition2(el) {
				var number = el.item.index;
				sync1.data('owl.carousel').to(number, 500, true);
			}
			
			sync2.on("click", ".owl-item", function (e) {
				e.preventDefault();
				if(!$(this).hasClass('current')) {
                    var number = $(this).index();
                    sync1.data('owl.carousel').to(number, 500, true);
                }
			});
		}
	};
	$(document).ready(G5PlusTestimonials.init);
	$(window).resize(G5PlusTestimonials.init);
	$(window).load(G5PlusTestimonials.sync_slider);
}(jQuery));