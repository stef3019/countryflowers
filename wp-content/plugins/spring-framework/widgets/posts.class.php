<?php
if (!defined('ABSPATH')) {
	exit;
//	Exit if accessed directly
}
if (!class_exists('G5P_Widget_Posts')) {
	class G5P_Widget_Posts extends GSF_Widget
	{
		public function __construct()
		{
			$this->widget_cssclass = 'widget-posts';
			$this->widget_id = 'gsf-posts';
			$this->widget_name = esc_html__('G5Plus: Posts', 'spring-framework');

			$this->settings = array(
				'fields' => array(
					array(
						'id'      => 'title',
						'title'   => esc_html__('Title:', 'spring-framework'),
						'type'    => 'text',
						'default' => '',
					),
					array(
						'id'      => 'source',
						'type'    => 'select',
						'title'   => esc_html__('Source', 'spring-framework'),
						'default' => 'recent',
						'options' => array(
							'random'   => esc_html__('Random', 'spring-framework'),
							'popular'  => esc_html__('Popular', 'spring-framework'),
							'recent'   => esc_html__('Recent', 'spring-framework'),
							'oldest'   => esc_html__('Oldest', 'spring-framework'),
							'video'    => esc_html__('Video', 'spring-framework'),
						)
					),
					array(
						'id'         => 'posts_per_page',
						'type'       => 'text',
						'input_type' => 'number',
						'title'      => esc_html__('Number of posts to show:', 'spring-framework'),
						'default'    => '4',
					),
					array(
						'id'      => 'post_animation',
						'type'    => 'select',
						'title'   => esc_html__('Animation', 'spring-framework'),
						'default' => '',
						'options' => G5P()->settings()->get_animation(true),
					),
				)
			);
			parent::__construct();
		}

		function widget($args, $instance)
		{
			extract($args, EXTR_SKIP);
			$title = (!empty($instance['title'])) ? $instance['title'] : '';
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);
			$source = (!empty($instance['source'])) ? $instance['source'] : 'recent';
			$posts_per_page = (!empty($instance['posts_per_page'])) ? absint($instance['posts_per_page']) : 4;
			$post_animation = (!empty($instance['post_animation'])) ? $instance['post_animation'] : '';

			$query_args = array(
				'posts_per_page'      => $posts_per_page,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'post_type'           => 'post',
				'tax_query'           => array(
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => array('post-format-link', 'post-format-quote', 'post-format-audio'),
						'operator' => 'NOT IN'
					),
				)
			);

			$query_order_args = array();
			switch ($source) {
				case 'random' :
					$query_order_args = array(
						'orderby' => 'rand',
						'order'   => 'DESC',
					);
					break;
				case 'popular':
					$query_order_args = array(
						'orderby' => 'comment_count',
						'order'   => 'DESC',
					);
					break;
				case 'recent':
					$query_order_args = array(
						'orderby' => 'post_date',
						'order'   => 'DESC',
					);
					break;
				case 'oldest':
					$query_order_args = array(
						'orderby' => 'post_date',
					);
					break;
				case 'video':
					$query_order_args = array(
						'tax_query' => array(
							array(
								'taxonomy' => 'post_format',
								'field'    => 'slug',
								'terms'    => array('post-format-video'),
								'operator' => 'IN'
							),
						)
					);
					break;
			}
			$post_inner_class = "";
			if(function_exists('Spring_Plant')) {
			    if($post_animation == '') {
			        $post_animation = Spring_Plant()->options()->get_post_animation();
                }
                $post_inner_class = Spring_Plant()->helper()->getCSSAnimation($post_animation);
            }


			$query_args = array_merge($query_args, $query_order_args);

            $r = new WP_Query($query_args);
            if ($r->have_posts()) :
                echo wp_kses_post($args['before_widget']);
                if ($title) {
                    echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
                } ?>
                <div class="widget-posts">
                    <?php while ($r->have_posts()) : $r->the_post(); ?>
                        <article <?php post_class("clearfix post-default"); ?>>
                            <div class="<?php echo esc_attr($post_inner_class); ?>">
                                <?php if (function_exists('Spring_Plant')) {
                                    Spring_Plant()->blog()->render_post_thumbnail_markup(array(
                                        'image_size' => 'blog-widget',
                                        'placeholder_enable' => true,
                                        'mode' => 'full',
                                        'image_mode'         => 'image'
                                    ));
                                } ?>
                                <div class="entry-content-wrap">
									<h3 class="gf-post-title"><a title="<?php the_title(); ?>"
																 href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
                                    <div class="gf-post-date-meta">
                                        <?php echo get_the_date( 'F j Y' ) ?>
                                    </div>
                                   
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php echo wp_kses_post($args['after_widget']); ?>
                <?php
                // Reset the global $the_post as this query will have stomped on it
                wp_reset_postdata();
            endif;
		}
	}
}
