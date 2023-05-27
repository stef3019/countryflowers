var G5_Woocommerce = window.G5_Woocommerce || {};
(function ($) {
    "use strict";
    window.G5_Woocommerce = G5_Woocommerce;

    var $body = $('body'),
        isLazy = $body.hasClass('gf-lazy-load'),
        isRTL = $body.hasClass('rtl'),
        isAjx = false;

    G5_Woocommerce = {
        init: function () {
            this.initSwitchLayout();
            this.processTabs();
            this.updateAjaxPosts();
            this.addToWishlist();
            this.addToCart();
            this.quickView();
            this.addCartQuantity();
            this.initFilterBellow();
            this.initFilterAjax();
            this.initPerfectSroll();
            var $productImageWrap_slick = $('#single-product-image-3');
            if ($productImageWrap_slick.length) {
                this.singleProductImageSlick($productImageWrap_slick);
            }
            var $productImageWrap = $('#single-product-image');
            if ($productImageWrap.length) {
                this.singleProductImage($productImageWrap);
            }
            setTimeout(function () {
                G5_Woocommerce.setCartScrollBar();
            }, 500);
            this.saleCountdown();
            this.yith_wcan_ajax_filtered();
            this.events();
            this.productSize();
        },
        isDesktop: function () {
            var responsive_breakpoint = 1199;
            return window.matchMedia('(min-width: ' + (responsive_breakpoint + 1) + 'px)').matches;
        },
        initFilterAjax: function () {
            $(document).on('click', '.clear-filter-wrap a, .gf-price-filter a, .gf-product-sorting a, .gf-attr-filter-content a, .gf-product-category-filter a', function (e) {
                e.preventDefault();
                execFilterAjax($(this));
            });
            $(document).on('archive-product-ajax', function (event, target) {
                execFilterAjax($(target));
            });
            function execFilterAjax($target) {
                var ajax_type = $target.closest('[data-items-paging="load-more"]').length ? 'load_more' : ($target.closest('[data-items-paging="infinite-scroll"]').length ? 'infinitive' : 'reload'),
                    is_paging = ($target.closest('[data-items-paging="load-more"]').length || $target.closest('[data-items-paging="infinite-scroll"]').length || $target.closest('[data-items-paging="pagination-ajax"]').length || $target.closest('[data-items-paging="next-prev"]').length) ? true : false,
                    is_cate_filter = ($target.closest('[data-items-cate]').length || $target.closest('.gf-product-category-filter').length) ? true : false,
                    $wrapper = $('[data-archive-content][data-items-wrapper]');
                if (is_cate_filter) {
                    if ($target.parent().hasClass('active')) {
                        return false;
                    }
                }
                G5_Woocommerce.showLoading($wrapper, ajax_type);
                var pageUrl = $target.attr('href'),
                    cache = G5_Core.cache.getCache(pageUrl, 'archive_shop');
                if (!isAjx) {
                    isAjx = true;
                    if (cache !== '') {
                        G5_Woocommerce.onSuccess($wrapper, $target, cache, pageUrl, ajax_type, is_paging, is_cate_filter);
                        isAjx = false;
                    } else {
                        $.ajax({
                            url: pageUrl,
                            data: {
                                shop_load_type: 'ajax'
                            },
                            dataType: 'html',
                            cache: false,
                            headers: {'cache-control': 'no-cache'},
                            method: 'POST',
                            error: function (xhr) {
                                console.log(xhr.error);
                                isAjx = false;
                                G5_Core.loading_content.hideLoading($wrapper);
                            },
                            success: function (response) {
                                G5_Woocommerce.onSuccess($wrapper, $target, response, pageUrl, ajax_type, is_paging, is_cate_filter);
                                G5_Core.cache.addCache(pageUrl, response, 'archive_shop');
                                isAjx = false;
                            }
                        });
                    }
                }
            }
        },
        onSuccess: function ($wrapper, $target, response, pageUrl, ajax_type, is_paging, is_cate_filter) {
            var $ajaxHTML = $('<div>' + response + '</div>'),
                catalog_filter_wrap = $wrapper.find('.gsf-catalog-filter'),
                switch_layout = $wrapper.find('.gf-shop-switch-layout'),
                catalog_filter = catalog_filter_wrap.find('.woocommerce-custom-wrap'),
                ajax_catalog_filter = $ajaxHTML.find('.gsf-catalog-filter').find('.woocommerce-custom-wrap'),
                filter_content = catalog_filter_wrap.find('#gf-filter-content'),
                ajax_filter_content = $ajaxHTML.find('.gsf-catalog-filter').find('#gf-filter-content'),
                canvas_filter_inner = $('#canvas-filter-wrapper').find('.canvas-sidebar-inner'),
                ajax_canvas_filter_inner = $ajaxHTML.find('#canvas-filter-wrapper').find('.canvas-sidebar-inner'),
                clear_filter = catalog_filter_wrap.find('.clear-filter-wrap'),
                ajax_clear_filter = $ajaxHTML.find('.gsf-catalog-filter').find('.clear-filter-wrap'),
                ajax_container = $ajaxHTML.find('[data-items-container]'),
                wpTitle = $ajaxHTML.find('#ajax-page-title').text(),
                container = $wrapper.find('[data-items-container]'),
                isotope = container.hasClass('isotope'),
                $page_title = $('.gf-page-title h1'),
                animation = ($wrapper.find('.gf_animate_when_almost_visible').length > 0),
                paging = $wrapper.find('[data-items-paging]'),
                ajax_paging = $ajaxHTML.find('[data-items-paging]');
            if (ajax_container.length) {
                $ajaxHTML.find('.gf-shop-switch-layout').html(switch_layout.html());
                if (!animation) {
                    ajax_container.find('article').css('opacity', 0);
                }
                if ('reload' === ajax_type) {
                    if (wpTitle.length && is_cate_filter) {
                        // Update document/page title
                        document.title = wpTitle;
                        if ($page_title.length) {
                            $page_title.text(wpTitle);
                        }
                    }
                    catalog_filter.html(ajax_catalog_filter.html());
                    if (!is_paging) {
                        filter_content.html(ajax_filter_content.html());
                        if (canvas_filter_inner.length && ajax_canvas_filter_inner.length) {
                            canvas_filter_inner.html(ajax_canvas_filter_inner.html());
                            canvas_filter_inner.perfectScrollbar('update');
                        }
                    }
                    G5_Woocommerce.initPerfectSroll();
                    container.html(ajax_container.html());
                } else {
                    container.append(ajax_container.html());
                }
                if (!is_paging) {
                    if (clear_filter.length && ajax_clear_filter.length) {
                        clear_filter.html(ajax_clear_filter.html());
                    }
                }
                if (is_cate_filter) {
                    $wrapper.find('[data-items-cate]').find('li.active').removeClass('active');
                    var $href = $target.attr('href');
                    $wrapper.find('a[href="' + $href + '"]').parent().addClass('active');
                }
                $wrapper.imagesLoaded({background: true}, function () {
                    if (isotope) {
                        var config = container.data("isotope-options");
                        if (typeof(config) !== 'undefined') {
                            if (ajax_type !== 'infinitive') {
                                if ((typeof (config.masonry) !== 'undefined')
                                    && (typeof (config.masonry.columnWidth) !== 'undefined')
                                    && (config.masonry.columnWidth === '.gsf-column-base')) {
                                    container.append('<article class="gsf-column-base"></article>');
                                }
                                container.isotope('reloadItems').isotope();
                            } else {
                                container.isotope('appended', ajax_container.html());
                            }

                            G5_Core.isotope.layout(container);
                            if ((typeof (config.masonry) !== 'undefined')
                                && (typeof (config.masonry.columnWidth) !== 'undefined')
                                && (config.masonry.columnWidth === '.gsf-column-base')) {
                                $(window).trigger('resize');
                            }
                        }
                    }
                    G5_Core.loading_content.hideLoading($wrapper);
                    if (!animation) {
                        container.find('article').animate({opacity: 1}, 500, 'easeInQuad');
                    } else {
                        new G5_Core_Animation($wrapper);
                    }
                    G5_Core.lazyLoad.init($wrapper);
                    if ('reload' === ajax_type) {
                        G5_Core.util.setPushState(pageUrl);
                    }
                    G5_Core.off_canvas.init();
                    G5_Woocommerce.widgetSliderInit();
                    if (paging.length) {
                        if (ajax_paging.length) {
                            paging.html(ajax_paging.html());
                        } else {
                            paging.html('');
                        }
                    } else {
                        if (ajax_paging.length) {
                            $wrapper.append(ajax_paging);
                        }
                    }
                    G5_Core.util.tooltip();
                    $( '.woocommerce-ordering' ).off('change').on( 'change', 'select.orderby', function() {
                        $( this ).closest( 'form' ).submit();
                    });
                });
            } else {
                G5_Core.loading_content.hideLoading($wrapper);
                if ($ajaxHTML.find('.woocommerce-info').length) {
                    var no_item_found = $ajaxHTML.find('article').addClass('col-sm-12');
                    container.html(no_item_found);
                }
            }
        },
        initPerfectSroll: function () {
            $('.gf-product-category-filter-wrap .gf-product-category-filter').perfectScrollbar({
                wheelSpeed: 0.5,
                suppressScrollX: true
            });
        },
        widgetSliderInit: function () {
            $('.price_slider').each(function () {
                var $this = $(this),
                    $parent = $(this).closest('.price_slider_wrapper'),
                    min_price = $parent.find('#min_price').data('min'),
                    max_price = $parent.find('#max_price').data('max'),
                    current_min_price = parseInt($parent.find('#min_price').val(), 10),
                    current_max_price = parseInt($parent.find('#max_price').val(), 10);
                if (isNaN(current_min_price)) {
                    current_min_price = parseInt(min_price, 10)
                }

                if (isNaN(current_max_price)) {
                    current_max_price = parseInt(max_price, 10)
                }

                $('input#min_price, input#max_price', $parent).hide();
                $('.price_slider, .price_label', $parent).show();
                $this.slider({
                    range: true,
                    animate: true,
                    min: min_price,
                    max: max_price,
                    values: [current_min_price, current_max_price],
                    create: function () {
                        $parent.find('#min_price').val(current_min_price);
                        $parent.find('#max_price').val(current_max_price);
                        $(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
                    },
                    slide: function (event, ui) {

                        $parent.find('#min_price').val(ui.values[0]);
                        $parent.find('#max_price').val(ui.values[1]);

                        $(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
                    },
                    change: function (event, ui) {
                        $(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
                    }
                });
            });
        },
        showLoading: function ($wrapper, ajax_type) {
            var $container = $wrapper.find('[data-items-container]'),
                $wrapper_height = $wrapper.outerHeight(),
                $loading = $wrapper.children('.gsf-content-loading'),
                wrapperOffset = $wrapper.offset().top - G5_Core.util.getScrollOffset(),
                $header = $('.header-sticky'),
                $top = ($container.offset().top - $wrapper.offset().top),
                animation = ($wrapper.find('.gf_animate_when_almost_visible').length > 0);
            if ($header.length) {
                wrapperOffset -= 80;
            }
            if ('reload' === ajax_type) {
                $top += 100;
                var bodyTop = document.documentElement['scrollTop'] || document.body['scrollTop'],
                    delta = bodyTop - wrapperOffset,
                    scrollSpeed = Math.abs(delta) / 2;
                if (scrollSpeed < 800) scrollSpeed = 800;
                $('html,body').animate({scrollTop: wrapperOffset}, scrollSpeed, 'easeInOutCubic');
            } else {
                $top = $wrapper.height();
                if ('load_more' === ajax_type) {
                    $top -= 100;
                }
            }
            $loading.css('top', $top);
            $wrapper.css('height', $wrapper_height).addClass('loading');
            if ('reload' === ajax_type) {
                if (!animation) {
                    $container.find($wrapper.find('article')).animate({opacity: 0}, 500, 'easeOutQuad');
                } else {
                    $container.find('.gf_animate_when_almost_visible').addClass('zoom-reverse');
                }
            }
        },
        singleProductImageSlick: function ($productImageWrap) {
            var vertical = true,
                rtl = $('body').hasClass('rtl'),
                single_main = $productImageWrap.find('.single-product-image-main'),
                zoom_image = single_main.find('.zoom-image'),
                main_image = single_main.find('img').attr('srcset', ''),
                $thumb = $productImageWrap.find('.single-product-image-thumb'),
                time_out = null,
                visibleItems = [],
                itemToShow = 5,
                option = 0;

            $thumb.slick({
                swipeToSlide: true,
                infinite: false,
                slidesToShow: itemToShow,
                speed: 400,
                arrows: false,
                vertical: vertical,
                verticalSwiping: vertical,
                rtl: rtl,
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: itemToShow,
                            vertical: false,
                            verticalSwiping: false

                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 3,
                            vertical: false,
                            verticalSwiping: false,
                            variableWidth: true
                        }
                    }
                ]
            });

            $thumb.on("mouseenter mouseleave", ".slick-slide", function (e) {
                e.preventDefault();
                var number = $(this).data("slick-index");
                if ($(this).hasClass('synced')) return;
                var selector = $(this).find('[data-index="' + number + '"]'),
                    large_img = selector.attr('data-large-image'),
                    origin_img = selector.attr('href');
                $(this).closest('.slick-slider').find('.slick-slide').removeClass('synced');
                $(this).addClass('synced');
                zoom_image.attr('href', origin_img);
                if (time_out != null) {
                    clearTimeout(time_out);
                }
                time_out = setTimeout(function () {
                    main_image.fadeOut(function () {
                        main_image.attr('src', large_img).fadeIn();
                        G5_Core.util.magnificPopup();
                    });
                }, 400);
            });

            $(document).on('found_variation', function (event, variation) {
                if ((typeof variation !== 'undefined') && (typeof variation.variation_id !== 'undefined')) {
                    var variation_id = variation.variation_id,
                        selector = $('a[data-variation_id*="|' + variation_id + '|"]', $thumb),
                        slick_current = selector.closest('.slick-slide');
                    slick_current.trigger('mouseenter');
                }
            });

            $(document).on('reset_data', function (event) {
                $('a[data-index="0"]', $thumb).closest('.slick-slide').trigger('mouseenter');
            });
        },
        initSwitchLayout: function () {
            var handle = false;
            $(document).on('click', '.gf-shop-switch-layout li a', function (event) {
                event.preventDefault();
                var $this = $(this),
                    $layout = $this.data('layout'),
                    product_wrap = $this.closest('.gsf-product-wrap').find('[data-items-container="true"]'),
                    paging = $this.closest('.gsf-product-wrap').find('.gf-paging');
                if (!$this.closest('li').hasClass('active') && !handle) {
                    handle = true;
                    $this.closest('.gf-shop-switch-layout').children('li').removeClass('active');
                    $this.closest('li').addClass('active');
                    //product_wrap.fadeOut();
                    paging.fadeOut();
                    $this.waypoint(function () {
                        $(this.element).addClass("wpb_start_animation animated");
                        this.destroy();
                    });
                    product_wrap.fadeOut(function () {
                        if ('list' === $layout) {
                            product_wrap.removeClass('layout-grid').addClass('layout-list');
                        } else {
                            product_wrap.removeClass('layout-list').addClass('layout-grid');
                        }
                        G5_Core.util.tooltip();
                        product_wrap.fadeIn('slow');
                        paging.fadeIn('slow');
                    });

                    $.cookie('product_layout', $layout, {expires: 15});
                    handle = false;
                }
            });
        },
        processTabs: function () {
            $('.gsf-pretty-tabs').each(function () {
                var $this = $(this),
                    heading, heading_width, wrap_row;
                if ($this.closest('.custom-product-row').length > 0 || $this.closest('.custom-product-heading-row').length > 0) {
                    if ($this.closest('.custom-product-row').length > 0) {
                        wrap_row = $this.closest('.custom-product-row');
                    } else {
                        wrap_row = $this.closest('.custom-product-heading-row');
                    }
                    heading = wrap_row.find('.gf-heading');
                    if (heading.length > 0) {
                        heading_width = heading.find('.heading-title').outerWidth();
                        if (isRTL) {
                            if (wrap_row.hasClass('heading-right')) {
                                $this.css('margin-left', (heading_width + 30));
                            } else {
                                $this.css('margin-right', (heading_width + 30));
                            }

                        } else {
                            if (wrap_row.hasClass('heading-right')) {
                                $this.css('margin-right', (heading_width + 30));
                            } else {
                                $this.css('margin-left', (heading_width + 30));
                            }
                        }
                        $this.gsfPrettyTabs();
                    }
                }
            });
        },
        updateAjaxPosts: function () {
            var _that = this;
            $body.on('gf_pagination_ajax_success', function (event, data, $ajaxHTML) {
                if (data.settings['post_type'] === 'product') {
                    _that.updateResultCount($(event.target), data, $ajaxHTML);
                    $(event.target).imagesLoaded({background: true}, function () {
                        $(event.target).trigger('gf_update_ajax_product', [data]);
                        G5_Core.util.tooltip();
                        G5_Woocommerce.saleCountdown();
                    });
                }
            });
        },
        updateResultCount: function ($wrapper, data, $ajaxHTML) {
            var $resultCount = $wrapper.find('.woocommerce-result-count'),
                $resultCountResult = $ajaxHTML.find('.woocommerce-result-count');
            if (($resultCount.length === 0) || ($resultCountResult.length === 0)) return;
            $resultCount.html($resultCountResult.html());
        },
        initFilterBellow: function () {
            $(document).on('click', '.gf-filter-bellow', function () {
                $('#gf-filter-content').slideToggle('500');
                G5_Woocommerce.initPerfectSroll();
            });

            if ($('.gf-fiter-type-select').length) {
                $('.gf-fiter-type-select .gf-attr-filter-content').slideUp('100');
            }
            if ($('.gf-product-category-filter-select').length) {
                $('.gf-product-category-filter-select .gf-product-category-filter').slideUp('100');
            }
            $(document).on('click', '.gf-fiter-type-select .filter-select-open', function (e) {
                var select = $(e.target).closest('.gf-fiter-type-select').toggleClass('opened');
                if (select.hasClass('opened')) {
                    $('.gf-attr-filter-content', select).slideDown('500');
                } else {
                    $('.gf-attr-filter-content', select).slideUp('fast');
                }
            });
            $(document).on('click', '.gf-product-category-filter-select .gf-filter-open', function (e) {
                var select = $(e.target).closest('.gf-product-category-filter-select').toggleClass('opened');
                if (select.hasClass('opened')) {
                    $('.gf-product-category-filter', select).slideDown('500');
                } else {
                    $('.gf-product-category-filter', select).slideUp('fast');
                }
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.gf-fiter-type-select').length) {
                    $('.gf-fiter-type-select').removeClass('opened').find('.gf-attr-filter-content').slideUp('fast');
                }
                if (!$(e.target).closest('.gf-product-category-filter-select').length) {
                    $('.gf-product-category-filter-select').removeClass('opened').find('.gf-product-category-filter').slideUp('fast');
                }
            });
        },
        addToWishlist: function () {
            $(document).on('click', '.add_to_wishlist', function () {
                var button = $(this),
                    buttonWrap = button.parent().parent();

                if (!buttonWrap.parent().hasClass('single-product-function')) {
                    button.addClass("added-spinner");
                    var productWrap = buttonWrap.parent().parent().parent().parent();
                    if (typeof(productWrap) === 'undefined') {
                        return;
                    }
                    productWrap.addClass('active');
                }
            });

            $body.on("added_to_wishlist", function (event, fragments, cart_hash, $thisbutton) {
                var button = $('.added-spinner.add_to_wishlist'),
                    buttonWrap = button.parent().parent();
                if (!buttonWrap.parent().hasClass('single-product-function')) {
                    var productWrap = buttonWrap.parent().parent().parent().parent();
                    if (typeof(productWrap) === 'undefined') {
                        return;
                    }
                    setTimeout(function () {
                        productWrap.removeClass('active');
                        button.removeClass('added-spinner');
                    }, 700);
                }
            });
        },
        addToCart: function () {
            $(document).on('click', '.add_to_cart_button', function () {
                var button = $(this);
                if (!button.hasClass('single_add_to_cart_button') && button.is('.product_type_simple')) {
                    var productWrap = button.closest('.product-item-inner');
                    if (typeof(productWrap) === 'undefined') {
                        return;
                    }
                    productWrap.addClass('active');
                }
            });

            $body.on('adding_to_cart', function (event, button, data) {
                var button_wrap = button.closest('.product-action-item'),
                    ladda_button = null;
                button.attr('data-spinner-color', button.css('color'));
                button.data('spinner-color', button.css('color'));
                if (button.closest('.product-list-actions').length === 0) {
                    if (button.hasClass('ladda-button') && button.length > 0) {
                        ladda_button = Ladda.create(button[0]);
                        ladda_button.start(function () {
                            button_wrap.css('width', button_wrap.width());
                        });
                        $(window).on('resize', function () {
                            button_wrap.css('width', '');
                        });
                    }
                } else {
                    if (button.hasClass('ladda-button') && button.length > 0) {
                        ladda_button = Ladda.create(button[0]);
                        ladda_button.start();
                    }
                }
            });
            $body.on('wc_cart_button_updated', function (event, $button) {
                var header_sticky = $('.header-sticky-wrapper .header-sticky'),
                    mini_cart = $('.item-shopping-cart', header_sticky);
                var is_single_product = $button.hasClass('single_add_to_cart_button');

                if (is_single_product) return;

                var buttonWrap = $button.parent(),
                    buttonViewCart = buttonWrap.find('.added_to_cart'),
                    addedTitle = buttonViewCart.text(),
                    productWrap = buttonWrap.closest('.product-item-inner');

                $button.remove();
                if (!$button.is('.add_to_cart_list')) {
                    buttonViewCart.html('<i class="fa fa-check"></i> ' + addedTitle);
                } else {
                    buttonViewCart.html(addedTitle);
                }
                if ((buttonViewCart.closest('.product-skin-01').length === 0) && (buttonViewCart.closest('.product-skin-07').length === 0)) {
                    setTimeout(function () {
                        buttonWrap.tooltip('hide').attr('title', addedTitle).tooltip('_fixTitle');
                    }, 500);
                }
                setTimeout(function () {
                    G5_Woocommerce.setCartScrollBar(function () {
                        if (mini_cart.length > 0) {
                            var timeOut = 0;
                            if (header_sticky.hasClass('header-hidden')) {
                                header_sticky.removeClass('header-hidden');
                                timeOut = 500;
                            }
                            setTimeout(function () {
                                mini_cart.addClass('show-cart');
                                setTimeout(function () {
                                    mini_cart.removeClass('show-cart');
                                }, 2000);
                            }, timeOut);
                        }
                    });
                }, 10);
                setTimeout(function () {
                    productWrap.removeClass('active');
                }, 700);
            });
            $body.on('removed_from_cart', function () {
                setTimeout(function () {
                    G5_Woocommerce.setCartScrollBar();
                    $(document.body).trigger('update_checkout');
                    var update_cart = $('[name="update_cart"]');
                    if (update_cart.length) {
                        update_cart.removeAttr('disabled').trigger('click');
                    }
                }, 10);
            });
        },
        quickView: function () {
            var is_click_quick_view = false;
            $(document).on('click', '.product-quick-view', function (event) {
                event.preventDefault();
                if (is_click_quick_view) return;

                is_click_quick_view = true;
                var $this = $(this),
                    product_id = $this.data('product_id'),
                    popupWrapper = '#popup-product-quick-view-wrapper',
                    $icon = $this.find('i'),
                    iconClass = $icon.attr('class'),
                    productWrap = $this.parent().parent().parent().parent(),
                    button = $this,
                    is_pagination = ($this.hasClass('prev-product') || $this.hasClass('next-product'));
                productWrap.addClass('active');
                button.addClass('active');
                $icon.attr('class', 'fa fa-spinner fa-pulse');
                $.ajax({
                    url: spring_plant_variable.ajax_url,
                    data: {
                        action: 'product_quick_view',
                        id: product_id
                    },
                    success: function (html) {
                        productWrap.removeClass('active');
                        button.removeClass('active');
                        $icon.attr('class', iconClass);
                        var modal_body = $('.modal-body', popupWrapper);
                        if (!is_pagination) {
                            if ($(popupWrapper).length) {
                                $(popupWrapper).remove();
                            }
                            $body.append(html);
                            var $productImageWrap = $('.quick-view-product-image', popupWrapper);
                            G5_Woocommerce.singleProductImage($productImageWrap);
                            if (typeof $.fn.wc_variation_form !== 'undefined') {
                                var form_variation = $(popupWrapper).find('.variations_form');
                                var form_variation_select = $(popupWrapper).find('.variations_form .variations select');
                                form_variation.wc_variation_form();
                                form_variation.trigger('check_variations');
                                form_variation_select.change();
                            }
                            $(popupWrapper).modal();
                            G5_Core.util.tooltip();
                            G5_Woocommerce.saleCountdown();
                            $(popupWrapper).find('.single-product-image-thumb a').off('click').on('click', function (e) {
                                e.preventDefault();
                            });
                        } else {
                            var modal_content = $('.modal-content', popupWrapper),
                                quickview_navigation = $('.product-quickview-navigation', popupWrapper);
                            modal_content.css({
                                width: modal_content.width(),
                                height: modal_content.height()
                            });
                            modal_body.fadeOut(function () {
                                modal_body.html($('.modal-body', html).html()).fadeIn();
                                quickview_navigation.html($('.product-quickview-navigation', html).html());
                                var $productImageWrap = $('.quick-view-product-image', modal_body);
                                G5_Woocommerce.singleProductImage($productImageWrap);
                                if (typeof $.fn.wc_variation_form !== 'undefined') {
                                    var form_variation = $(popupWrapper).find('.variations_form');
                                    var form_variation_select = $(popupWrapper).find('.variations_form .variations select');
                                    form_variation.wc_variation_form();
                                    form_variation.trigger('check_variations');
                                    form_variation_select.change();
                                }
                                $(popupWrapper).addClass('in');
                                $(popupWrapper).fadeIn();
                                setTimeout(function () {
                                    modal_content.css({
                                        width: '',
                                        height: ''
                                    });
                                }, 1000);
                                G5_Core.util.tooltip();
                                G5_Woocommerce.saleCountdown();
                                $(modal_content).find('.single-product-image-thumb a').off('click').on('click', function (e) {
                                    e.preventDefault();
                                });
                            });
                        }

                        G5_Woocommerce.addCartQuantity();
                        is_click_quick_view = false;
                    },
                    error: function (html) {
                        is_click_quick_view = false;
                    }
                });

            });
        },
        addCartQuantity: function () {
            $(document).off('click', '.quantity .btn-number').on('click', '.quantity .btn-number', function (event) {
                event.preventDefault();
                var type = $(this).data('type'),
                    input = $('input', $(this).parent()),
                    current_value = parseFloat(input.val()),
                    max = parseFloat(input.attr('max')),
                    min = parseFloat(input.attr('min')),
                    step = parseFloat(input.attr('step')),
                    stepLength = 0;
                if (input.attr('step').indexOf('.') > 0) {
                    stepLength = input.attr('step').split('.')[1].length;
                }

                if (isNaN(max)) {
                    max = 100;
                }
                if (isNaN(min)) {
                    min = 0;
                }
                if (isNaN(step)) {
                    step = 1;
                    stepLength = 0;
                }

                if (!isNaN(current_value)) {
                    if (type == 'minus') {
                        if (current_value > min) {
                            current_value = (current_value - step).toFixed(stepLength);
                            input.val(current_value).change();
                        }

                        if (parseFloat(input.val()) <= min) {
                            input.val(min).change();
                            $(this).attr('disabled', true);
                        }
                    }

                    if (type == 'plus') {
                        if (current_value < max) {
                            current_value = (current_value + step).toFixed(stepLength);
                            input.val(current_value).change();
                        }
                        if (parseFloat(input.val()) >= max) {
                            input.val(max).change();
                            $(this).attr('disabled', true);
                        }
                    }
                } else {
                    input.val(min);
                }
            });


            $('input', '.quantity').on('focusin', function () {
                $(this).data('oldValue', $(this).val());
            });

            $('input', '.quantity').on('change', function () {
                var input = $(this),
                    max = parseFloat(input.attr('max')),
                    min = parseFloat(input.attr('min')),
                    current_value = parseFloat(input.val()),
                    step = parseFloat(input.attr('step'));

                if (isNaN(max)) {
                    max = 100;
                }
                if (isNaN(min)) {
                    min = 0;
                }

                if (isNaN(step)) {
                    step = 1;
                }


                var btn_add_to_cart = $('.add_to_cart_button', $(this).parent().parent().parent());
                if (current_value >= min) {
                    $(".btn-number[data-type='minus']", $(this).parent()).removeAttr('disabled');
                    if (typeof(btn_add_to_cart) != 'undefined') {
                        btn_add_to_cart.attr('data-quantity', current_value);
                    }

                } else {
                    alert('Sorry, the minimum value was reached');
                    $(this).val($(this).data('oldValue'));

                    if (typeof(btn_add_to_cart) != 'undefined') {
                        btn_add_to_cart.attr('data-quantity', $(this).data('oldValue'));
                    }
                }

                if (current_value <= max) {
                    $(".btn-number[data-type='plus']", $(this).parent()).removeAttr('disabled');
                    if (typeof(btn_add_to_cart) != 'undefined') {
                        btn_add_to_cart.attr('data-quantity', current_value);
                    }
                } else {
                    alert('Sorry, the maximum value was reached');
                    $(this).val($(this).data('oldValue'));
                    if (typeof(btn_add_to_cart) != 'undefined') {
                        btn_add_to_cart.attr('data-quantity', $(this).data('oldValue'));
                    }
                }

            });
        },
        setCartScrollBar: function (callback) {
            $('.cart_list.product_list_widget').perfectScrollbar({
                wheelSpeed: 0.5,
                suppressScrollX: true
            });
            if (callback) {
                callback();
            }
        },
        singleProductImage: function ($productImageWrap) {
            var single_main = $productImageWrap.find('.single-product-image-main'),
                zoom_image = single_main.find('.zoom-image'),
                main_image = single_main.find('img').attr('srcset', ''),
                slider_thumb = $productImageWrap.find('.single-product-image-thumb'),
                time_out = null,
                items_show = 5;
            slider_thumb.attr('hidden', 'hidden');
            if (slider_thumb.closest('.quick-view-product-image').length !== 0) {
                items_show = 4;
            }
            slider_thumb.on('initialized.owl.carousel', function (event) {
                slider_thumb.find(".owl-item").eq(0).addClass("current");
                setTimeout(function () {
                    slider_thumb.removeAttr('hidden');
                }, 1000);
            }).owlCarousel({
                items: 5,
                nav: false,
                dots: false,
                rtl: isRTL,
                lazyLoad: isLazy,
                margin: 10,
                responsive: {
                    992: {
                        items: items_show
                    },
                    768: {
                        items: 3
                    }
                }
            }).on('changed.owl.carousel', syncPosition);

            function syncPosition(el) {
                var selector = $(el.target).find('[data-index="' + el.item.index + '"]'),
                    large_img = selector.attr('data-large-image'),
                    origin_img = selector.attr('href'),
                    owl_current = selector.closest('.owl-item');
                $(el.target).find('.owl-item').removeClass('current');
                owl_current.addClass('current');
                zoom_image.attr('href', origin_img);
                if (time_out != null) {
                    clearTimeout(time_out);
                }
                time_out = setTimeout(function () {
                    main_image.stop().animate({opacity: 0}, function () {
                        main_image.attr('src', large_img);
                        main_image.imagesLoaded({src: true}, function () {
                            main_image.animate({opacity: 1});
                            G5_Core.util.magnificPopup();
                        });
                    });
                }, 200);
            }

            slider_thumb.on("mouseenter mouseleave", ".owl-item", function (e) {
                e.preventDefault();
                if ($(this).hasClass('current')) return;
                var number = $(this).index();
                var selector = $(this).find('[data-index="' + number + '"]'),
                    large_img = selector.attr('data-large-image'),
                    origin_img = selector.attr('href');
                $(this).closest('.owl-carousel').find('.owl-item').removeClass('current');
                $(this).addClass('current');
                zoom_image.attr('href', origin_img);
                if (time_out != null) {
                    clearTimeout(time_out);
                }
                time_out = setTimeout(function () {
                    main_image.stop().animate({opacity: 0}, function () {
                        main_image.attr('src', large_img);
                        main_image.imagesLoaded({src: true}, function () {
                            main_image.animate({opacity: 1});
                            G5_Core.util.magnificPopup();
                        });
                    });
                }, 200);
            });

            $(document).on('found_variation', function (event, variation) {
                if ((typeof variation !== 'undefined') && (typeof variation.variation_id !== 'undefined')) {
                    var variation_id = variation.variation_id,
                        selector = $('a[data-variation_id*="|' + variation_id + '|"]', slider_thumb),
                        owl_current = selector.closest('.owl-item');
                    owl_current.trigger('mouseenter');
                }
            });

            $(document).on('reset_data', function (event) {
                $('a[data-index="0"]', slider_thumb).closest('.owl-item').trigger('mouseenter');
            });
        },
        saleCountdown: function () {
            $('.product-deal-countdown').each(function () {
                var date_end = $(this).data('date-end');
                var $this = $(this);
                $this.countdown(date_end, function (event) {
                    count_down_callback(event, $this);
                }).on('update.countdown', function (event) {
                    count_down_callback(event, $this);
                });
            });

            function count_down_callback(event, $this) {
                var seconds = parseInt(event.offset.seconds);
                var minutes = parseInt(event.offset.minutes);
                var hours = parseInt(event.offset.hours);
                var days = parseInt(event.offset.totalDays);

                if (days < 10) days = '0' + days;
                if (hours < 10) hours = '0' + hours;
                if (minutes < 10) minutes = '0' + minutes;
                if (seconds < 10) seconds = '0' + seconds;


                $('.countdown-day', $this).text(days);
                $('.countdown-hours', $this).text(hours);
                $('.countdown-minutes', $this).text(minutes);
                $('.countdown-seconds', $this).text(seconds);
            }

            G5_Woocommerce.saleCountdownWidth();
        },
        saleCountdownWidth: function () {
            $('.product-deal-countdown').each(function () {
                var innerWidth = 0;
                $(this).removeClass('small');
                $('.countdown-section', $(this)).each(function () {
                    innerWidth += $(this).outerWidth() + parseInt($(this).css('margin-left').replace("px", ''), 10);
                });
                if (innerWidth > $(this).outerWidth()) {
                    $(this).addClass('small');
                }
            });
        },
        yith_wcan_ajax_filtered: function () {
            $(document).on("yith-wcan-ajax-filtered", function (event, response) {
                var $wrapper = $body;

                if ((typeof (yith_wcan) !== 'undefined')
                    && (typeof (yith_wcan.container) !== 'undefined')) {
                    $wrapper = $(yith_wcan.container);
                }
                new G5_Core_Animation($wrapper);
                $wrapper.find('.gsf-pretty-tabs').gsfPrettyTabs({
                    more_text: spring_plant_variable.pretty_tabs_more_text
                });
                G5_Core.lazyLoad.init();
                G5_Core.off_canvas.init();
                G5_Core.util.tooltip();
            });
        },
        events: function () {
            var time_out = null;
            $(window).on('resize', function () {
                if (time_out != null) {
                    clearTimeout(time_out);
                }
                time_out = setTimeout(function () {
                    G5_Woocommerce.processTabs();
                    G5_Woocommerce.productSize()
                }, 200);
            });
            $('[data-items-wrapper].products').on('gf_pagination_ajax_success', function () {
                G5_Woocommerce.productSize();
            });
        },
        productSize: function () {
            $('.product-item-wrap').each(function () {
                $(this).removeClass('product-small');
                if ($(this).width() < 300) {
                    $(this).addClass('product-small');
                }
            });
        }
    };

    $(document).ready(function () {
        G5_Woocommerce.init();
    });

})(jQuery);
