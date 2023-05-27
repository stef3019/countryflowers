<?php
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}

if (!class_exists('G5P_Inc_Config_Meta_Boxes')) {
    class G5P_Inc_Config_Meta_Boxes
    {
	    /*
	 * loader instances
	 */
	    private static $_instance;

	    public static function getInstance()
	    {
		    if (self::$_instance == NULL) {
			    self::$_instance = new self();
		    }

		    return self::$_instance;
	    }

        public function init()
        {
            // Defined Theme Options
            add_filter('gsf_meta_box_config', array($this, 'register_meta_boxes'));
            add_action('do_meta_boxes',array($this,'remove_my_page_meta_boxes'));
        }

        public function remove_my_page_meta_boxes() {
            $screen = get_current_screen();
            if ( is_admin() && ($screen->id == 'page') ) {
                global $post;
                $id = $post->ID;
                if (('page' == get_option('show_on_front') && $id == get_option('page_for_posts')) || (class_exists('WooCommerce') && $id == get_option( 'woocommerce_shop_page_id' ))) {
                    remove_meta_box( 'gsf_page_setting',$this->getPostType(),'advanced' ); // Author Metabox
                }
            }
        }

	    public function getPostType() {
		    return apply_filters('gsf_page_setting_post_type', array('page', 'product'));
	    }

        public function register_meta_boxes($configs)
        {
            $prefix = G5P()->getMetaPrefix();

            /**
             * CUSTOM PAGE SETTINGS
             */
            $configs['gsf_page_setting'] = array(
                'name' => esc_html__('Page Settings', 'spring-framework'),
                'post_type' => $this->getPostType(),
                'layout' => 'inline',
	            'section' => array(
		            array(
			            'id' =>  "{$prefix}section_general",
			            'title' => esc_html__('General', 'spring-framework'),
			            'icon' => 'dashicons dashicons-admin-site',
			            'fields' => array(
				            G5P()->configOptions()->get_config_preset(array('id' => "{$prefix}page_preset")),
				            array(
					            'id' => "{$prefix}group_layout",
					            'type' => 'group',
					            'title' => esc_html__('Layout','spring-framework'),
					            'fields' => array(
						            array(
							            'id' => "{$prefix}main_layout",
							            'title' => esc_html__('Site Layout', 'spring-framework'),
							            'type' => 'image_set',
							            'options' => G5P()->settings()->get_main_layout(true),
							            'default' => '',
						            ),

						            G5P()->configOptions()->get_config_toggle(array(
							            'id' => "{$prefix}content_full_width",
							            'title' => esc_html__('Content Full Width', 'spring-framework'),
							            'subtitle' => esc_html__('Turn On this option if you want to expand the content area to full width.', 'spring-framework'),
							            'default' => '',
						            ),true),

						            G5P()->configOptions()->get_config_toggle(array(
							            'id' => "{$prefix}custom_content_padding",
							            'title' => esc_html__('Custom Content Padding','spring-framework'),
							            'subtitle' => esc_html__('Turn On this option if you want to custom content padding.', 'spring-framework'),
							            'default' => ''
						            )),
						            array(
							            'id' => "{$prefix}content_padding",
							            'title' => esc_html__('Content Padding', 'spring-framework'),
							            'subtitle' => esc_html__('Set content padding', 'spring-framework'),
							            'type' => 'spacing',
							            'default' => array('left' => 0, 'right' => 0, 'top' => 50, 'bottom' => 50),
							            'required' => array("{$prefix}custom_content_padding",'=','on')
						            ),

                                    G5P()->configOptions()->get_config_toggle(array(
                                        'id' => "{$prefix}custom_content_padding_mobile",
                                        'title' => esc_html__('Custom Content Padding Mobile','spring-framework'),
                                        'subtitle' => esc_html__('Turn On this option if you want to custom content padding mobile.', 'spring-framework'),
                                        'default' => ''
                                    )),
                                    array(
                                        'id' => "{$prefix}content_padding_mobile",
                                        'title' => esc_html__('Content Padding Mobile', 'spring-framework'),
                                        'subtitle' => esc_html__('Set content padding mobile', 'spring-framework'),
                                        'type' => 'spacing',
                                        'default' => array('left' => 0, 'right' => 0, 'top' => 50, 'bottom' => 50),
                                        'required' => array("{$prefix}custom_content_padding_mobile",'=','on')
                                    ),

						            G5P()->configOptions()->get_config_sidebar_layout(array(
							            'id' => "{$prefix}sidebar_layout",
						            ),true),
						            G5P()->configOptions()->get_config_sidebar(array(
							            'id' => "{$prefix}sidebar",
							            'required' => array("{$prefix}sidebar_layout",'!=','none')
						            )),
                                    G5P()->configOptions()->get_config_toggle(array(
                                        'id' => "{$prefix}above_content_enable",
                                        'title' => esc_html__('Above Content Enable', 'spring-framework'),
                                        'subtitle' => esc_html__('Turn Off this option if you want to disable Above content', 'spring-framework'),
                                        'default' => ''
                                    ), true),
                                    G5P()->configOptions()->get_config_content_block(array(
                                        'id' => "{$prefix}above_content_block",
                                        'subtitle' => esc_html__('Specify the Content Block to use as a Above content.', 'spring-framework'),
                                        'required' => array("{$prefix}above_content_enable", '!=', 'off')
                                    )),
                                    array(
                                        'id' => "{$prefix}above_content_margin_bottom",
                                        'title' => esc_html__('Above Content Margin Bottom', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter number of margin bottom for Above content (default unit is px)', 'spring-framework'),
                                        'type' => 'text',
                                        'input_type' => 'number',
                                        'default' => 50,
                                        'required' => array("{$prefix}above_content_enable", '!=', 'off')
                                    )
					            )
				            ),

				            array(
					            'id' => "{$prefix}group_page_title",
					            'type' => 'group',
					            'title' => esc_html__('Page Title','spring-framework'),
					            'fields' => array(
						            G5P()->configOptions()->get_config_toggle(array(
							            'title' => esc_html__('Page Title Enable','spring-framework'),
							            'id' => "{$prefix}page_title_enable"
						            ),true),
						            G5P()->configOptions()->get_config_content_block(array(
							            'id' => "{$prefix}page_title_content_block",
							            'desc' => esc_html__('Specify the Content Block to use as a page title content.', 'spring-framework'),
							            'required' => array("{$prefix}page_title_enable", '!=', 'off')
						            ),true),

						            array(
							            'title'       => esc_html__('Custom Page title', 'spring-framework'),
							            'id'          => "{$prefix}page_title_content",
							            'type'        => 'text',
							            'default'     => '',
							            'required' => array("{$prefix}page_title_enable", '!=', 'off'),
							            'desc'        => esc_html__('Enter custom page title for this page', 'spring-framework')
						            ),
                                    array(
                                        'title' => esc_html__('Custom Page Subtitle', 'spring-framework'),
                                        'id' => "{$prefix}page_subtitle_content",
                                        'type' => 'text',
                                        'default' => '',
                                        'required' => array("{$prefix}page_title_enable", '!=', 'off'),
                                        'desc' => esc_html__('Enter custom page subtitle for this page', 'spring-framework')
                                    )
					            )
				            ),
				            array(
					            'title'        => esc_html__('Custom Css Class', 'spring-framework'),
					            'id'          => "{$prefix}css_class",
					            'type'        => 'selectize',
					            'tags' => true,
					            'default'         => '',
					            'desc'        => esc_html__('Enter custom class for this page', 'spring-framework')
				            )
			            )
		            ),
		            array(
			            'id' => "{$prefix}section_menu",
			            'title' => esc_html__('Menu', 'spring-framework'),
			            'icon' => 'dashicons dashicons-menu',
			            'fields' => array(
				            array(
					            'id' => "{$prefix}page_menu",
					            'title' => esc_html__('Page Menu', 'spring-framework'),
					            'type' => 'selectize',
					            'allow_clear' => true,
					            'placeholder' => esc_html__('Select Menu', 'spring-framework'),
					            'desc' => esc_html__('Optionally you can choose to override the menu that is used on the page', 'spring-framework'),
					            'data' => 'menu'
				            ),
				            array(
					            'id' => "{$prefix}page_mobile_menu",
					            'title' => esc_html__('Page Mobile Menu', 'spring-framework'),
					            'type' => 'selectize',
					            'allow_clear' => true,
					            'placeholder' => esc_html__('Select Menu', 'spring-framework'),
					            'desc' => esc_html__('Optionally you can choose to override the menu mobile that is used on the page', 'spring-framework'),
					            'data' => 'menu'
				            ),
				            G5P()->configOptions()->get_config_toggle(array(
					            'id' => "{$prefix}is_one_page",
					            'title' => esc_html__('Is One Page', 'spring-framework'),
					            'desc' => esc_html__('Set page style is One Page', 'spring-framework'),
				            ))
			            )
		            ),
	            ),
            );

            /**
             * CUSTOME POST SETTING
             */
            $configs['gsf_post_setting'] = array(
                'name' => esc_html__('Post Settings', 'spring-framework'),
                'post_type' => array('post'),
                'layout' => 'inline',
                'section' => array(
                    array(
                        'id' =>  "{$prefix}section_post_general",
                        'title' => esc_html__('General', 'spring-framework'),
                        'icon' => 'dashicons dashicons-admin-site',
                        'fields' => array(
                            array(
                                'id' => "gf_format_video_embed",
                                'title' => esc_html__('Featured Video/Audio Code','spring-framework'),
                                'subtitle' => esc_html__('Paste YouTube, Vimeo or self hosted video URL then player automatically will be generated.','spring-framework'),
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => "gf_format_audio_embed",
                                'title' => esc_html__('Featured Video/Audio Code','spring-framework'),
                                'subtitle' => esc_html__('Paste YouTube, Vimeo or self hosted video URL then player automatically will be generated.','spring-framework'),
                                'type' => 'textarea'
                            ),
                            array(
                                'id' => "gf_format_gallery_images",
                                'title' => esc_html__('Featured Gallery','spring-framework'),
                                'subtitle' => esc_html__('Select images for featured gallery. (Apply for post format gallery)','spring-framework'),
                                'type' => 'gallery'
                            ),
                            array(
                                'id' => "gf_format_link_url",
                                'title' => esc_html__('Featured Link','spring-framework'),
                                'subtitle' => esc_html__('Enter featured link. (Apply for post format link)','spring-framework'),
                                'type' => 'text'
                            ),
                            array(
                                'id' => "{$prefix}single_post_layout",
                                'title' => esc_html__('Post Layout', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your post layout', 'spring-framework'),
                                'type' => 'image_set',
                                'options' => G5P()->settings()->get_single_post_layout(true),
                                'default' => ''
                            ),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}custom_single_image_padding",
                                'title' => esc_html__('Custom Single Image Padding','spring-framework'),
                                'default' => '',
                                'required' => array("{$prefix}single_post_layout", '=', 'layout-5')
                            )),
                            array(
                                'id' => "{$prefix}post_single_image_padding",
                                'title' => esc_html__('Single Image Padding', 'spring-framework'),
                                'subtitle' => esc_html__('Set single image padding', 'spring-framework'),
                                'type' => 'spacing',
                                'default' => array('left' => 0, 'right' => 0, 'top' => 0, 'bottom' => 0),
                                'required' => array("{$prefix}custom_single_image_padding", '=', 'on')
                            ),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}custom_single_image_mobile_padding",
                                'title' => esc_html__('Custom Single Image Mobile Padding','spring-framework'),
                                'default' => '',
                                'required' => array("{$prefix}single_post_layout", '=', 'layout-5')
                            )),
                            array(
                                'id' => "{$prefix}post_single_image_mobile_padding",
                                'title' => esc_html__('Single Image Mobile Padding', 'spring-framework'),
                                'subtitle' => esc_html__('Set single image mobile padding', 'spring-framework'),
                                'type' => 'spacing',
                                'default' => array('left' => 0, 'right' => 0, 'top' => 0, 'bottom' => 0),
                                'required' => array("{$prefix}custom_single_image_mobile_padding", '=', 'on')
                            ),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_reading_process_enable",
                                'title' => esc_html__('Reading Process', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to hide reading process on single blog', 'spring-framework'),
                                'default' => ''
                            ), true),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_tag_enable",
                                'title' => esc_html__('Tags', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to hide tags on single blog', 'spring-framework'),
                                'default' => ''
                            ), true),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_share_enable",
                                'title' => esc_html__('Share', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to hide share on single blog', 'spring-framework'),
                                'default' => ''
                            ), true),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_navigation_enable",
                                'title' => esc_html__('Navigation', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to hide navigation on single blog', 'spring-framework'),
                                'default' => ''
                            ), true),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_author_info_enable",
                                'title' => esc_html__('Author Info', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to hide author info area on single blog', 'spring-framework'),
                                'default' => ''
                            ), true)
                        )
                    ),
                    array(
                        'id' =>  "{$prefix}section_post_related",
                        'title' => esc_html__('Related Posts', 'spring-framework'),
                        'icon' => 'dashicons dashicons-images-alt2',
                        'fields' => array(
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_related_post_enable",
                                'title' => esc_html__('Related Posts', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to hide related posts area on single blog', 'spring-framework'),
                                'default' => ''
                            ), true),
                            array(
                                'id' => "{$prefix}single_related_post_algorithm",
                                'title' => esc_html__('Related Posts Algorithm', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the algorithm of related posts', 'spring-framework'),
                                'type' => 'select',
                                'options' => G5P()->settings()->get_related_post_algorithm(true),
                                'default' => '',
                                'required' => array("{$prefix}single_related_post_enable",'in',array('on', ''))
                            ),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_related_post_carousel_enable",
                                'title' => esc_html__('Carousel Mode', 'spring-framework'),
                                'subtitle' => esc_html__('Turn On this option if you want to enable carousel mode', 'spring-framework'),
                                'default' => '',
                                'required' => array("{$prefix}single_related_post_enable",'in',array('on', ''))
                            ), true),
                            array(
                                'id' => "{$prefix}single_related_post_per_page",
                                'title' => esc_html__('Posts Per Page', 'spring-framework'),
                                'subtitle' => esc_html__('Enter number of posts per page you want to display', 'spring-framework'),
                                'type' => 'text',
                                'input_type' => 'number',
                                'default' => '',
                                'required' => array("{$prefix}single_related_post_enable",'in',array('on', ''))
                            ),
                            array(
                                'id' => "{$prefix}single_related_post_columns_gutter",
                                'title' => esc_html__('Post Columns Gutter', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your horizontal space between post.', 'spring-framework'),
                                'type' => 'select',
                                'options' => G5P()->settings()->get_post_columns_gutter(true),
                                'default' => '',
                                'required' => array("{$prefix}single_related_post_enable",'in',array('on', ''))
                            ),
                            array(
                                'id' => "{$prefix}single_related_post_columns_group",
                                'title' => esc_html__('Post Columns', 'spring-framework'),
                                'type' => 'group',
                                'required' => array("{$prefix}single_related_post_enable",'in',array('on', '')),
                                'fields' => array(
                                    array(
                                        'id' => "{$prefix}single_related_post_columns_row_1",
                                        'type' => 'row',
                                        'col' => 3,
                                        'fields' => array(
                                            array(
                                                'id' => "{$prefix}single_related_post_columns",
                                                'title' => esc_html__('Large Devices', 'spring-framework'),
                                                'desc' => esc_html__('Specify your post columns on large devices (>= 1200px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout' => 'full',
                                            ),
                                            array(
                                                'id' => "{$prefix}single_related_post_columns_md",
                                                'title' => esc_html__('Medium Devices', 'spring-framework'),
                                                'desc' => esc_html__('Specify your post columns on medium devices (>= 992px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout' => 'full',
                                            ),
                                            array(
                                                'id' => "{$prefix}single_related_post_columns_sm",
                                                'title' => esc_html__('Small Devices', 'spring-framework'),
                                                'desc' => esc_html__('Specify your post columns on small devices (>= 768px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout' => 'full',
                                            ),
                                            array(
                                                'id' => "{$prefix}single_related_post_columns_xs",
                                                'title' => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                'desc' => esc_html__('Specify your post columns on extra small devices (< 768px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout' => 'full',
                                            ),
                                            array(
                                                'id' => "{$prefix}single_related_post_columns_mb",
                                                'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                'desc' => esc_html__('Specify your post columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout' => 'full',
                                            )
                                        )
                                    ),
                                )
                            ),
                            array(
                                'id' => "{$prefix}single_related_post_paging",
                                'title' => esc_html__('Post Paging', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your post paging mode', 'spring-framework'),
                                'type' => 'select',
                                'options' => G5P()->settings()->get_post_paging_small_mode(true),
                                'default' => '',
                                'required' => array(
                                    array("{$prefix}single_related_post_carousel_enable",'!=', 'on'),
                                    array("{$prefix}single_related_post_enable",'in',array('on', ''))
                                )
                            ),
                            array(
                                'id' => "{$prefix}single_related_post_animation",
                                'title' => esc_html__('Animation', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your post animation', 'spring-framework'),
                                'type' => 'select',
                                'options' => G5P()->settings()->get_animation(true),
                                'default' => '',
                                'required' => array("{$prefix}single_related_post_enable",'in',array('on', ''))
                            )
                        )
                    ),
                    array(
                        'id' =>  "{$prefix}section_general",
                        'title' => esc_html__('Layout', 'spring-framework'),
                        'icon' => 'dashicons dashicons-editor-table',
                        'fields' => array(
                            G5P()->configOptions()->get_config_preset(array('id' => "{$prefix}page_preset")),
                            array(
                                'id' => "{$prefix}group_layout",
                                'type' => 'group',
                                'title' => esc_html__('Layout','spring-framework'),
                                'fields' => array(
                                    array(
                                        'id' => "{$prefix}main_layout",
                                        'title' => esc_html__('Site Layout', 'spring-framework'),
                                        'type' => 'image_set',
                                        'options' => G5P()->settings()->get_main_layout(true),
                                        'default' => '',
                                    ),

                                    G5P()->configOptions()->get_config_toggle(array(
                                        'id' => "{$prefix}content_full_width",
                                        'title' => esc_html__('Content Full Width', 'spring-framework'),
                                        'subtitle' => esc_html__('Turn On this option if you want to expand the content area to full width.', 'spring-framework'),
                                        'default' => '',
                                    ),true),

                                    G5P()->configOptions()->get_config_toggle(array(
                                        'id' => "{$prefix}custom_content_padding",
                                        'title' => esc_html__('Custom Content Padding','spring-framework'),
                                        'subtitle' => esc_html__('Turn On this option if you want to custom content padding.', 'spring-framework'),
                                        'default' => ''
                                    )),
                                    array(
                                        'id' => "{$prefix}content_padding",
                                        'title' => esc_html__('Content Padding', 'spring-framework'),
                                        'subtitle' => esc_html__('Set content padding', 'spring-framework'),
                                        'type' => 'spacing',
                                        'default' => array('left' => 0, 'right' => 0, 'top' => 50, 'bottom' => 50),
                                        'required' => array("{$prefix}custom_content_padding",'=','on')
                                    ),

                                    G5P()->configOptions()->get_config_toggle(array(
                                        'id' => "{$prefix}custom_content_padding_mobile",
                                        'title' => esc_html__('Custom Content Padding Mobile','spring-framework'),
                                        'subtitle' => esc_html__('Turn On this option if you want to custom content padding mobile.', 'spring-framework'),
                                        'default' => ''
                                    )),
                                    array(
                                        'id' => "{$prefix}content_padding_mobile",
                                        'title' => esc_html__('Content Padding Mobile', 'spring-framework'),
                                        'subtitle' => esc_html__('Set content padding mobile', 'spring-framework'),
                                        'type' => 'spacing',
                                        'default' => array('left' => 0, 'right' => 0, 'top' => 50, 'bottom' => 50),
                                        'required' => array("{$prefix}custom_content_padding_mobile",'=','on')
                                    ),

                                    G5P()->configOptions()->get_config_sidebar_layout(array(
                                        'id' => "{$prefix}sidebar_layout",
                                    ),true),
                                    G5P()->configOptions()->get_config_sidebar(array(
                                        'id' => "{$prefix}sidebar",
                                        'required' => array("{$prefix}sidebar_layout",'!=','none')
                                    )),
                                    G5P()->configOptions()->get_config_toggle(array(
                                        'id' => "{$prefix}above_content_enable",
                                        'title' => esc_html__('Above Content Enable', 'spring-framework'),
                                        'subtitle' => esc_html__('Turn Off this option if you want to disable Above content', 'spring-framework'),
                                        'default' => ''
                                    ), true),
                                    G5P()->configOptions()->get_config_content_block(array(
                                        'id' => "{$prefix}above_content_block",
                                        'subtitle' => esc_html__('Specify the Content Block to use as a Above content.', 'spring-framework'),
                                        'required' => array("{$prefix}above_content_enable", '!=', 'off')
                                    )),
                                    array(
                                        'id' => "{$prefix}above_content_margin_bottom",
                                        'title' => esc_html__('Above Content Margin Bottom', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter number of margin bottom for Above content (default unit is px)', 'spring-framework'),
                                        'type' => 'text',
                                        'input_type' => 'number',
                                        'default' => 50,
                                        'required' => array("{$prefix}above_content_enable", '!=', 'off')
                                    )
                                )
                            ),

                            array(
                                'id' => "{$prefix}group_page_title",
                                'type' => 'group',
                                'title' => esc_html__('Page Title','spring-framework'),
                                'fields' => array(
                                    G5P()->configOptions()->get_config_toggle(array(
                                        'title' => esc_html__('Page Title Enable','spring-framework'),
                                        'id' => "{$prefix}page_title_enable"
                                    ),true),
                                    G5P()->configOptions()->get_config_content_block(array(
                                        'id' => "{$prefix}page_title_content_block",
                                        'desc' => esc_html__('Specify the Content Block to use as a page title content.', 'spring-framework'),
                                        'required' => array("{$prefix}page_title_enable", '!=', 'off')
                                    ),true),

                                    array(
                                        'title'       => esc_html__('Custom Page title', 'spring-framework'),
                                        'id'          => "{$prefix}page_title_content",
                                        'type'        => 'text',
                                        'default'     => '',
                                        'required' => array("{$prefix}page_title_enable", '!=', 'off'),
                                        'desc'        => esc_html__('Enter custom page title for this page', 'spring-framework')
                                    )
                                )
                            ),
                            array(
                                'title'        => esc_html__('Custom Css Class', 'spring-framework'),
                                'id'          => "{$prefix}css_class",
                                'type'        => 'selectize',
                                'tags' => true,
                                'default'         => '',
                                'desc'        => esc_html__('Enter custom class for this page', 'spring-framework')
                            )
                        )
                    ),
                    array(
                        'id' => "{$prefix}section_menu",
                        'title' => esc_html__('Menu', 'spring-framework'),
                        'icon' => 'dashicons dashicons-menu',
                        'fields' => array(
                            array(
                                'id' => "{$prefix}page_menu",
                                'title' => esc_html__('Page Menu', 'spring-framework'),
                                'type' => 'selectize',
                                'allow_clear' => true,
                                'placeholder' => esc_html__('Select Menu', 'spring-framework'),
                                'desc' => esc_html__('Optionally you can choose to override the menu that is used on the page', 'spring-framework'),
                                'data' => 'menu'
                            ),
                            array(
                                'id' => "{$prefix}page_mobile_menu",
                                'title' => esc_html__('Page Mobile Menu', 'spring-framework'),
                                'type' => 'selectize',
                                'allow_clear' => true,
                                'placeholder' => esc_html__('Select Menu', 'spring-framework'),
                                'desc' => esc_html__('Optionally you can choose to override the menu mobile that is used on the page', 'spring-framework'),
                                'data' => 'menu'
                            ),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}is_one_page",
                                'title' => esc_html__('Is One Page', 'spring-framework'),
                                'desc' => esc_html__('Set page style is One Page', 'spring-framework'),
                            ))
                        )
                    ),
                )
            );


            /**
             * CUSTOM PRODUCT SETTING
             */
            if(class_exists('WooCommerce')) {
                $configs['gsf_product_setting'] = array(
                    'name' => esc_html__('Product Settings', 'spring-framework'),
                    'post_type' => array('product'),
                    'layout' => 'inline',
                    'section' => array(
                        array(
                            'id' => "{$prefix}section_product_general",
                            'title' => esc_html__('Single Product', 'spring-framework'),
                            'icon' => 'dashicons dashicons-welcome-write-blog',
                            'fields' => array(
                                array(
                                    'id' => "{$prefix}product_single_layout",
                                    'title' => esc_html__('Product Single Layout', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify your product single layout', 'spring-framework'),
                                    'type' => 'image_set',
                                    'options' => G5P()->settings()->get_product_single_layout(true),
                                    'default' => ''
                                )
                            )
                        ),
                        array(
                            'id' => "{$prefix}section_product_related",
                            'title' => esc_html__('Related Products', 'spring-framework'),
                            'icon' => 'dashicons dashicons-images-alt2',
                            'fields' => array(
                                G5P()->configOptions()->get_config_toggle(array(
                                    'id' => "{$prefix}product_related_enable",
                                    'title' => esc_html__('Show Related Products', 'spring-framework'),
                                    'default' => ''
                                ), true),
                                array(
                                    'id' => "{$prefix}product_related_algorithm",
                                    'title' => esc_html__('Related Products Algorithm', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify the algorithm of related products', 'spring-framework'),
                                    'type' => 'select',
                                    'options' => G5P()->settings()->get_related_product_algorithm(true),
                                    'default' => '',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', ''))
                                ),
                                array(
                                    'id' => "{$prefix}product_related_item_skin",
                                    'title' => esc_html__('Products Skin', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify your related products skin', 'spring-framework'),
                                    'type' => 'image_set',
                                    'options' => G5P()->settings()->get_product_item_skin(true),
                                    'default' => '',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', ''))
                                ),
                                G5P()->configOptions()->get_config_toggle(array(
                                    'id' => "{$prefix}product_related_carousel_enable",
                                    'title' => esc_html__('Carousel Mode', 'spring-framework'),
                                    'subtitle' => esc_html__('Turn On this option if you want to enable carousel mode', 'spring-framework'),
                                    'default' => '',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', ''))
                                ), true),
                                array(
                                    'id' => "{$prefix}product_related_columns_gutter",
                                    'title' => esc_html__('Product Columns Gutter', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify your horizontal space between product.', 'spring-framework'),
                                    'type' => 'select',
                                    'options' => G5P()->settings()->get_post_columns_gutter(true),
                                    'default' => '',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', ''))
                                ),
                                array(
                                    'id' => "{$prefix}product_related_columns_group",
                                    'title' => esc_html__('Product Columns', 'spring-framework'),
                                    'type' => 'group',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', '')),
                                    'fields' => array(
                                        array(
                                            'id' => "{$prefix}product_related_columns_row_1",
                                            'type' => 'row',
                                            'col' => 3,
                                            'fields' => array(
                                                array(
                                                    'id' => "{$prefix}product_related_columns",
                                                    'title' => esc_html__('Large Devices', 'spring-framework'),
                                                    'desc' => esc_html__('Specify your related products columns on large devices (>= 1200px)', 'spring-framework'),
                                                    'type' => 'select',
                                                    'options' => G5P()->settings()->get_post_columns(true),
                                                    'default' => '',
                                                    'layout' => 'full',
                                                ),
                                                array(
                                                    'id' => "{$prefix}product_related_columns_md",
                                                    'title' => esc_html__('Medium Devices', 'spring-framework'),
                                                    'desc' => esc_html__('Specify your related products columns on medium devices (>= 992px)', 'spring-framework'),
                                                    'type' => 'select',
                                                    'options' => G5P()->settings()->get_post_columns(true),
                                                    'default' => '',
                                                    'layout' => 'full',
                                                ),
                                                array(
                                                    'id' => "{$prefix}product_related_columns_sm",
                                                    'title' => esc_html__('Small Devices', 'spring-framework'),
                                                    'desc' => esc_html__('Specify your related products columns on small devices (>= 768px)', 'spring-framework'),
                                                    'type' => 'select',
                                                    'options' => G5P()->settings()->get_post_columns(true),
                                                    'default' => '',
                                                    'layout' => 'full',
                                                ),
                                                array(
                                                    'id' => "{$prefix}product_related_columns_xs",
                                                    'title' => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                    'desc' => esc_html__('Specify your related products columns on extra small devices (< 768px)', 'spring-framework'),
                                                    'type' => 'select',
                                                    'options' => G5P()->settings()->get_post_columns(true),
                                                    'default' => '',
                                                    'layout' => 'full',
                                                ),
                                                array(
                                                    'id' => "{$prefix}product_related_columns_mb",
                                                    'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                    'desc' => esc_html__('Specify your related products columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                    'type' => 'select',
                                                    'options' => G5P()->settings()->get_post_columns(true),
                                                    'default' => '',
                                                    'layout' => 'full',
                                                )
                                            )
                                        ),
                                    )
                                ),
                                array(
                                    'id' => "{$prefix}product_related_per_page",
                                    'title' => esc_html__('Products Per Page', 'spring-framework'),
                                    'subtitle' => esc_html__('Enter number of products per page you want to display.', 'spring-framework'),
                                    'type' => 'text',
                                    'input_type' => 'number',
                                    'default' => '',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', ''))
                                ),
                                array(
                                    'id' => "{$prefix}product_related_animation",
                                    'title' => esc_html__('Animation', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify your product animation', 'spring-framework'),
                                    'type' => 'select',
                                    'options' => G5P()->settings()->get_animation(true),
                                    'default' => '',
                                    'required' => array("{$prefix}product_related_enable", 'in', array('on', ''))
                                )
                            )
                        )
                    )
                );
            }
            return $configs;
        }
    }
}