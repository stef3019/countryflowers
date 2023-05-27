(function ($) {
    "use strict";
    var G5_Post_Horizontal = window.G5_Post_Horizontal || {};
    window.G5_Post_Horizontal = G5_Post_Horizontal;
    G5_Post_Horizontal = {
        init: function () {
            $('.gf-post-horizontal').each(function () {
                G5_Post_Horizontal.execDots($(this));
            });
        },
        execDots: function ($post_horizontal) {
            var $item_container = $('[data-items-container]', $post_horizontal),
                $item_count = $('article', $item_container).length;
            if($item_count) {
                var $dot_html = '<div class="gsf-post-horizontal-dots">';
                for (var $i = 0; $i < $item_count; $i++) {
                    var $index = $i + 1;
                    $index = ($index < 10) ? ('0' + $index) : $index;
                    if($i == 0) {
                        $dot_html += '<span class="dot-index current" data-dot-index="' + $i + '">' + $index + '</span>';
                    } else {
                        $dot_html += '<span class="dot-index" data-dot-index="' + $i + '">' + $index + '</span>';
                    }
                }
                $dot_html += '</div>';
                $post_horizontal.append($dot_html);
                var $dot_container = $('.gsf-post-horizontal-dots', $post_horizontal);
                $item_container.on('changed.owl.carousel', function (el) {
                    var selector = $dot_container.find('[data-dot-index="' + el.item.index + '"]');
                    $dot_container.children().removeClass('current');
                    selector.addClass('current');
                });
                $dot_container.on('click', '.dot-index', function(e){
                    e.preventDefault();
                    if ($(this).hasClass('current')) return;
                    var number = $(this).attr('data-dot-index');
                    $dot_container.children().removeClass('current');
                    $(this).addClass('current');
                    $item_container.data('owl.carousel').to(number, 400, true);
                });
                $(document).on('reset_data',function(event){
                    $item_container.data('owl.carousel').to(0, 400, true);
                });
                G5_Post_Horizontal.execPosition($item_container, $dot_container);
            }
        },
        execPosition: function ($item_container, $dot_container) {
            var timeout = null;
            $item_container.on('owlInitialized', function () {
                if(window.matchMedia('(min-width: 1350px)').matches) {
                    G5_Post_Horizontal.execPositionContent($item_container, $dot_container);
                }
            });
            $(window).on('resize', function () {
                if (timeout !== null) {
                    clearTimeout(timeout);
                }
                timeout = setTimeout(function () {
                    $dot_container.css({
                        'padding-top': '',
                        'padding-right': '',
                        'max-height': ''
                    });
                    if(window.matchMedia('(min-width: 1350px)').matches) {
                        G5_Post_Horizontal.execPositionContent($item_container, $dot_container);
                    } else {
                        $dot_container.perfectScrollbar('destroy');
                    }
                }, 200);
            });
        },
        execPositionContent: function ($item_container, $dot_container) {
            var item_height = $item_container.find('.owl-item').height(),
                dot_height = $dot_container.height(),
                right_position = ($(window).width() - 1170) / 2;
            if(item_height >= dot_height) {
                var d = (item_height - dot_height)/2;
                $dot_container.css({
                    'margin-top': d,
                    'margin-bottom': d
                });
                $dot_container.perfectScrollbar('destroy');
            } else {
                $dot_container.css({
                    'margin-top': '',
                    'margin-bottom': '',
                    'padding-top': '20px',
                    'padding-right': '20px',
                    'max-height': item_height
                }).perfectScrollbar({
                    wheelSpeed: 0.5,
                    suppressScrollX: true
                });
            }
            $dot_container.css({
                'right': -(right_position - 60)
            })
        }
    };
    $(document).ready(function () {
        G5_Post_Horizontal.init();
    });
})
(jQuery);