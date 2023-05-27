<?php
/**
 * The template for displaying archive
 */
$gsf_query = Spring_Plant()->query()->get_query();
$post_settings = &Spring_Plant()->blog()->get_layout_settings();
$post_layout = isset( $post_settings['post_layout'] ) ? $post_settings['post_layout'] : 'large-image';
$post_animation = isset( $post_settings['post_animation'] ) ? $post_settings['post_animation'] : '';
$post_paging = isset( $post_settings['post_paging'] ) ? $post_settings['post_paging'] : 'pagination';
$layout_matrix = Spring_Plant()->blog()->get_layout_matrix( $post_layout );
$placeholder_enable = isset( $layout_matrix['placeholder_enable'] ) ? $layout_matrix['placeholder_enable'] : false;
$first_image_enable = isset( $layout_matrix['first_image_enable'] ) ? $layout_matrix['first_image_enable'] : false;
$post_item_skin = isset( $post_settings['post_item_skin'] ) ? $post_settings['post_item_skin'] : '';
if(!in_array($post_layout, array('grid', 'masonry'))) {
	$post_item_skin = '';
};
$category_filter_align = isset($post_settings['category_filter_align']) ? $post_settings['category_filter_align'] : '';
$image_size = isset($post_settings['post_image_size']) ? $post_settings['post_image_size'] :  Spring_Plant()->options()->get_post_image_size();
$image_size_base = $image_size;
$image_ratio = '';
if (in_array($post_layout, array('grid')) && ($image_size === 'full')) {
	$image_ratio = isset($post_settings['image_ratio']) ? $post_settings['image_ratio'] : '';
	if (empty($image_ratio)) {
		$image_ratio = Spring_Plant()->options()->get_post_image_ratio();
	}
	
	if ($image_ratio === 'custom') {
		$image_ratio_custom = isset($post_settings['image_ratio_custom']) ? $post_settings['image_ratio_custom'] : Spring_Plant()->options()->get_post_image_ratio_custom();
		if (is_array($image_ratio_custom) && isset($image_ratio_custom['width']) && isset($image_ratio_custom['height'])) {
			$image_ratio_custom_width = intval($image_ratio_custom['width']);
			$image_ratio_custom_height = intval($image_ratio_custom['height']);
			if (($image_ratio_custom_width > 0) && ($image_ratio_custom_height > 0)) {
				$image_ratio = "{$image_ratio_custom_width}x{$image_ratio_custom_height}";
			}
		} elseif (preg_match('/x/',$image_ratio_custom)) {
			$image_ratio = $image_ratio_custom;
		}
	}
	
	if ($image_ratio === 'custom') {
		$image_ratio = '1x1';
	}
}

$image_ratio_base = $image_ratio;

if ($post_layout === 'masonry') {
	$image_width = isset($post_settings['image_width']) ? $post_settings['image_width'] :  Spring_Plant()->options()->get_post_image_width();
	if (is_array($image_width) && isset($image_width['width'])) {
		$image_width = intval($image_width['width']);
	} else {
		$image_width = 400;
	}
	
	if ($image_width <= 0) {
		$image_width = 400;
	}
	$image_size = "{$image_width}x0";
}
$wrapper_attributes = array();
$inner_attributes = array();
$inner_classes = array(
	'gf-blog-inner',
	'clearfix',
	"layout-{$post_layout}",
	$post_item_skin
);

$post_inner_classes = array(
    'gf-post-inner',
    'clearfix',
    Spring_Plant()->helper()->getCSSAnimation( $post_animation )
);

$post_classes = array(
	'clearfix',
	'post-default',
	$post_item_skin
);

if ( isset( $post_settings['carousel'] ) ) {
	$inner_classes[] = 'owl-carousel owl-theme';
	if (isset($post_settings['carousel_class'])) {
		$inner_classes[] = $post_settings['carousel_class'];
	}
	$inner_attributes[] = "data-owl-options='" . json_encode( $post_settings['carousel'] ) . "'";
} else {
	if ( isset( $layout_matrix['columns_gutter'] ) ) {
		$inner_classes[] = "gf-gutter-{$layout_matrix['columns_gutter']}";
	} else {
		$inner_classes[] = 'row';
	}

	if ( isset( $layout_matrix['isotope'] ) ) {
		$inner_classes[] = 'isotope';
		$inner_attributes[] = "data-isotope-options='" . json_encode( $layout_matrix['isotope'] ) . "'";
		$wrapper_attributes[] = 'data-isotope-wrapper="true"';
	}
}

$wrapper_attributes[] = 'data-items-wrapper';
$inner_attributes[] = 'data-items-container="true"';

$paged = Spring_Plant()->query()->query_var_paged();

$inner_class = implode( ' ', array_filter( $inner_classes ) );
?>
<div <?php echo implode( ' ', $wrapper_attributes ); ?> class="gf-blog-wrap clearfix <?php echo esc_attr($category_filter_align); ?>">
	<?php
	// You can use this for adding codes before the main loop
	do_action( 'spring_plant_before_archive_wrapper' );
	?>
	<div <?php echo implode( ' ', $inner_attributes ); ?> class="<?php echo esc_attr( $inner_class ); ?>">
		<?php if ( Spring_Plant()->query()->have_posts() ) {
			if ( isset( $layout_matrix['layout'] ) ) {
				$layout_settings = $layout_matrix['layout'];
				$index = intval( $gsf_query->get( 'index', 0 ) );
                $carousel_index = 0;
				while ( Spring_Plant()->query()->have_posts() ) : Spring_Plant()->query()->the_post();
					$index = $index % count( $layout_settings );
					$current_layout = $layout_settings[$index];
					$isFirst = isset( $current_layout['isFirst'] ) ? $current_layout['isFirst'] : false;
					if ( $isFirst && ( $paged > 1 ) && in_array( $post_paging, array( 'load-more', 'infinite-scroll' ) ) ) {
						if ( isset( $layout_settings[$index + 1] ) ) {
							$current_layout = $layout_settings[$index + 1];
						} else {
							continue;
						}
					}
					$post_columns = $current_layout['columns'];
					$template = $current_layout['template'];
					$image_size = isset($current_layout['image_size'])  ? $current_layout['image_size'] : $image_size;

					$classes = array(
						"post-{$template}"
					);
                    if(isset($post_settings['carousel_rows']) && $carousel_index == 0) {
                        echo '<div class="gf-carousel-item clearfix">';
                    }
					if ( !isset( $post_settings['carousel'] )  || isset($post_settings['carousel_rows'])) {
						$classes[] = $post_columns;
					}

					$classes = wp_parse_args( $classes, $post_classes );

					$inner_classes = array();

					if ( is_sticky() && is_home() && !is_paged() && in_array( $template, array( 'large-image', 'medium-image', 'grid' ))) {
						$inner_classes[] = 'post-highlight';
					}
					$inner_classes = wp_parse_args( $inner_classes, $post_inner_classes );
					$post_class = implode( ' ', array_filter( $classes ) );
					$post_inner_class = implode( ' ', array_filter( $inner_classes ) );
					do_action( 'spring_plant_before_archive_post', array( 'post_class' => $post_class, 'post_inner_class' => $post_inner_class ) );
                    $post_link = has_post_format('link') ? get_post_meta(get_the_ID(), 'gf_format_link_url', true) : get_the_permalink();
					Spring_Plant()->helper()->getTemplate( "loop/layout/{$template}", array(
					    'image_size' => $image_size,
                        'post_class' => $post_class,
                        'post_inner_class' => $post_inner_class,
                        'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
                        'image_ratio' => $image_ratio,
						'post_item_skin' => $post_item_skin,
						'post_layout' => $post_layout,
                        'post_link' => $post_link
                    ));
					do_action( 'spring_plant_after_archive_post', array( 'post_class' => $post_class, 'post_inner_class' => $post_inner_class ) );
					if ( $isFirst ) {
						unset( $layout_settings[$index] );
						$layout_settings = array_values( $layout_settings );
					}

					if ( $isFirst && $paged === 1 ) {
						$index = 0;
					} else {
						$index++;
					}
                    $carousel_index++;
                    if(isset($post_settings['carousel_rows']) && $carousel_index == $post_settings['carousel_rows']['items_show']) {
                        echo '</div>';
                        $carousel_index = 0;
                    }
				endwhile;
                if(isset($post_settings['carousel_rows']) && $carousel_index != $post_settings['carousel_rows']['items_show'] && $carousel_index != 0) {
                    echo '</div>';
                }
			}
		} else if (isset($post_settings['isMainQuery'])) {
			Spring_Plant()->helper()->getTemplate( 'loop/content-none' );
		}
		?>
	</div>
	<?php
	// You can use this for adding codes before the main loop
	do_action( 'spring_plant_after_archive_wrapper' );
	?>
</div>



