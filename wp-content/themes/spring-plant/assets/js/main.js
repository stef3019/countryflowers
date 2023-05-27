(function ($) {
	"use strict";
	var G5_Main = window.G5_Main || {};
	window.G5_Main = G5_Main;

	var $window = $(window),
		$body = $('body'),
		isRTL = $body.hasClass('rtl'),
        isLazy = $body.hasClass('gf-lazy-load'),
		deviceAgent = navigator.userAgent.toLowerCase(),
		isMobile = deviceAgent.match(/(iphone|ipod|android|iemobile)/),
		isMobileAlt = deviceAgent.match(/(iphone|ipod|ipad|android|iemobile)/),
		isAppleDevice = deviceAgent.match(/(iphone|ipod|ipad)/),
		isIEMobile = deviceAgent.match(/(iemobile)/),
		bodyHeight = 0;

	G5_Main.blog = {
		init: function () {
			this.events();
			this.readingProcess();
		},
		readingProcess: function () {
			var reading_process = $('#gsf-reading-process'),
				post_content = $('.single-post .gf-single-wrap .post-single');
			if (reading_process.length > 0 && post_content.length > 0) {
				post_content.imagesLoaded(function () {
					var content_height = post_content.height(),
						window_height = $window.height();
					var percent = 0,
						content_offset = post_content.offset().top,
						window_offest = $window.scrollTop();

					if (window_offest > content_offset) {
						percent = 100 * (window_offest - content_offset) / (content_height - window_height);
					}
					if (percent > 100) {
						percent = 100;
					}
					reading_process.css('width', percent + '%');
					$window.scroll(function () {
						var percent = 0,
							content_offset = post_content.offset().top,
							window_offest = $window.scrollTop();

						if (window_offest > content_offset) {
							percent = 100 * (window_offest - content_offset) / (content_height - window_height);
						}
						if (percent > 100) {
							percent = 100;
						}
						reading_process.css('width', percent + '%');
					});
				});
			}
		},
		events: function () {
			var _that = this;
		}
	};
	G5_Main.header = {
		init: function () {
			this.login_link_event();
		},
		login_link_event: function () {
			var on_exec = false;
			$('.gsf-login-link-sign-in, .gsf-login-link-sign-up').off('click').on('click', function (event) {
				event.preventDefault();
				if(!on_exec) {
					on_exec = true;
                    var $this = $(this),
                        action_name = 'gsf_user_login',
                        ladda_button = null;
                    if ($this.hasClass('gsf-login-link-sign-up')) {
                        action_name = 'gsf_user_sign_up'
                    }
                    var popupWrapper = '#gsf-popup-login-wrapper';
                    $body.addClass('overflow-hidden');
                    $body.append('<div class="processing-title"><i class="fa fa-spinner fa-spin fa-fw"></i></div>');
                    $.ajax({
                        type: 'POST',
                        data: 'action=' + action_name,
                        url: spring_plant_variable.ajax_url,
                        success: function (html) {
                            $('.processing-title').fadeOut(function () {
                                $('.processing-title').remove();
                                $body.removeClass('overflow-hidden');
                            });
                            if ($(popupWrapper).length) {
                                $(popupWrapper).remove();
                            }
                            $body.append(html);

                            $(popupWrapper).modal();

                            $('#gsf-popup-login-form').submit(function (event) {
                                ladda_button = null;
                                var button = $(event.target).find('[type="submit"]');
                                if (button.hasClass('ladda-button') && button.length > 0) {
                                    ladda_button = Ladda.create(button[0]);
                                    ladda_button.start();
                                }
                                var input_data = $('#gsf-popup-login-form').serialize();
                                $body.addClass('overflow-hidden');
                                $body.append('<div class="processing-title"><i class="fa fa-spinner fa-spin fa-fw"></i></div>');
                                jQuery.ajax({
                                    type: 'POST',
                                    data: input_data,
                                    url: spring_plant_variable.ajax_url,
                                    success: function (html) {
                                        $('.processing-title').fadeOut(function () {
                                            $('.processing-title').remove();
                                            $body.removeClass('overflow-hidden');
                                        });
                                        var response_data = jQuery.parseJSON(html);
                                        if (response_data.code < 0) {
                                            jQuery('.login-message', '#gsf-popup-login-form').html(response_data.message);
                                        }
                                        else {
                                            window.location.reload();
                                        }
                                        if (ladda_button !== null) {
                                            ladda_button.stop();
                                            ladda_button.remove();
                                        }
                                    },
                                    error: function (html) {
                                        $('.processing-title').fadeOut(function () {
                                            $('.processing-title').remove();
                                            $body.removeClass('overflow-hidden');
                                        });
                                        if (ladda_button !== null) {
                                            ladda_button.stop();
                                            ladda_button.remove();
                                        }
                                    }
                                });
                                event.preventDefault();
                                return false;
                            });
                            on_exec = false;
                        },
                        error: function (html) {
                            $('.loading-wrapper').fadeOut(function () {
                                $('.loading-wrapper').remove();
                                $body.removeClass('overflow-hidden');
                            });
                            on_exec = false;
                        }
                    });
                }
			});
		}
	};
    G5_Main.page = {
    	init: function() {
			this.initInstagram();
		},
		initInstagram: function () {
			$('.instagram-5-columns .instagram-pics,.instagram-5-columns .zoom-instagram-widget__items').addClass('owl-carousel').owlCarousel({
				margin: 0,
				dots: false,
				nav: false,
                rtl: isRTL,
                lazyLoad: isLazy,
				responsive: {
                    992: {
                        items: 5
                    },
                    576: {
                        items: 3
                    },
					0: {
						items: 2
					}
				}
			});
        }
	};
    G5_Main.popup = {
        init: function () {
            var popupWrapper = $('#gsf-popup-mailchimp-wrapper');
            if (popupWrapper.length) {
                setTimeout(function() {
                    G5_Main.popup.showMailchimpPopup(popupWrapper)
                }, popupWrapper.data('mailchimp-popup-timeout'));
            }
        },

        showMailchimpPopup: function (popupWrapper) {
            if (popupWrapper.length) {
                popupWrapper.modal();
	            $('#remember-show', popupWrapper).on('change',function (){
		            if ($(this).is(':checked')) {
			            $.cookie('remember_show', true, {expires: 7, path: '/'});
		            } else {
			            $.cookie('remember_show', true, {expires: -1, path: '/'});
		            }
	            });
            }
        }
    };
	$(document).ready(function () {
		G5_Main.blog.init();
		G5_Main.header.init();
        G5_Main.page.init();
        G5_Main.popup.init();
	});
})
(jQuery);
