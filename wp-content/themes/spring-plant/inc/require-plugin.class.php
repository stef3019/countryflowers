<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Spring_Plant_Inc_Require_Plugin')) {
    class Spring_Plant_Inc_Require_Plugin
    {
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

            require_once(Spring_Plant()->themeDir('inc/libs/class-tgm-plugin-activation.php'));

            /*
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
            $plugins = array(
                array(
                    'name'               => esc_html__('Spring Framework', 'spring-plant'), // The plugin name
                    'slug'               => 'spring-framework', // The plugin slug (typically the folder name)
                    'source'             => get_template_directory() . '/inc/plugins/spring-framework-v3.0.zip', // The plugin source
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required
                    'version'            => '3.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
                    'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                    'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                    'external_url'       => '', // If set, overrides default API URL and points to an external URL
                ),
                array(
                    'name'               => 'Revolution Slider', // The plugin name
                    'slug'               => 'revslider', // The plugin slug (typically the folder name)
                    'source'             => get_template_directory() . '/inc/plugins/revslider_6.6.13.zip', // The plugin source
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required
                    'version'            => '6.6.13', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
                    'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                    'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                    'external_url'       => '', // If set, overrides default API URL and points to an external URL
                ),
                array(
                    'name'               => 'Visual Composer', // The plugin name
                    'slug'               => 'js_composer', // The plugin slug (typically the folder name)
                    'source'             => get_template_directory() . '/inc/plugins/js_composer_6.11.0.zip', // The plugin source
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required
                    'version'            => '6.11.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
                    'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                    'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                    'external_url'       => '', // If set, overrides default API URL and points to an external URL
                ),

                array(
                    'name'               => 'Envato Market', // The plugin name
                    'slug'               => 'envato-market', // The plugin slug (typically the folder name)
                    'source'             => get_template_directory() . '/inc/plugins/envato-market.zip', // The plugin source
                    'required'           => false, // If false, the plugin is only 'recommended' instead of required
                    'version'            => '2.0.8', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
                    'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                    'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                    'external_url'       => '', // If set, overrides default API URL and points to an external URL
                ),
                array(
                    'name' => 'WooCommerce', // The plugin name
                    'slug' => 'woocommerce', // The plugin slug (typically the folder name)
                    'required' => true, // If false, the plugin is only 'recommended' instead of required
                ),
                array(
                    'name' => 'YITH WooCommerce Wishlist', // The plugin name
                    'slug' => 'yith-woocommerce-wishlist', // The plugin slug (typically the folder name)
                    'required' => true, // If false, the plugin is only 'recommended' instead of required
                ),
                array(
                    'name' => 'YITH Woocommerce Compare', // The plugin name
                    'slug' => 'yith-woocommerce-compare', // The plugin slug (typically the folder name)
                    'required' => true, // If false, the plugin is only 'recommended' instead of required
                ),

                array(
                    'name' => 'YITH WooCommerce Ajax Navigation', // The plugin name
                    'slug' => 'yith-woocommerce-ajax-navigation', // The plugin slug (typically the folder name)
                    'required' => true, // If false, the plugin is only 'recommended' instead of required
                ),
                array(
                    'name'     => esc_html__('Contact Form 7', 'spring-plant'), // The plugin name
                    'slug'     => 'contact-form-7', // The plugin slug (typically the folder name)
                    'required' => false, // If false, the plugin is only 'recommended' instead of required
                ),
                array(
                    'name'     => esc_html__('WP Mail SMTP', 'spring-plant'), // The plugin name
                    'slug'     => 'wp-mail-smtp', // The plugin slug (typically the folder name)
                    'required' => false, // If false, the plugin is only 'recommended' instead of required
                ),
                array(
                    'name'     => esc_html__('MailChimp for WordPress', 'spring-plant'), // The plugin name
                    'slug'     => 'mailchimp-for-wp', // The plugin slug (typically the folder name)
                    'required' => false, // If false, the plugin is only 'recommended' instead of required
                ),
                array(
                    'name' => esc_html__('WPZOOM Social Feed Widget','spring-plant'),
                    'slug' => 'instagram-widget-by-wpzoom',
                    'required' => false,
                ),

            );

            /*
             * Array of configuration settings. Amend each line as needed.
             * If you want the default strings to be available under your own theme domain,
             * leave the strings uncommented.
             * Some of the strings are added into a sprintf, so see the comments at the
             * end of each line for what each argument will be.
             */

            // Change this to your theme text domain, used for internationalising strings
            $theme_text_domain = 'spring-plant';
            $config = array(
                'domain'       => $theme_text_domain,
                'id'           => 'spring_plant_theme_framework',// Unique ID for hashing notices for multiple instances of TGMPA.
                'default_path' => '',                      // Default absolute path to pre-packaged plugins.
                'menu'         => 'install-required-plugins', // Menu slug.
                'parent_slug'  => 'themes.php',            // Parent menu slug.
                'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
                'has_notices'  => true,                    // Show admin notices or not.
                'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
                'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => false,                   // Automatically activate plugins after installation or not.
                'message'      => '',                      // Message to output right before the plugins table.
                'strings'      => array(
                    'page_title'                      => esc_html__('Install Required Plugins', 'spring-plant'),
                    'menu_title'                      => esc_html__('Install Plugins', 'spring-plant'),
                    'installing'                      => esc_html__('Installing Plugin: %s', 'spring-plant'), // %s = plugin name.
                    'oops'                            => esc_html__('Something went wrong with the plugin API.', 'spring-plant'),
                    'notice_can_install_required'     => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_can_install_recommended'  => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_cannot_install'           => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_can_activate_required'    => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_cannot_activate'          => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_ask_to_update'            => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'spring-plant'), // %1$s = plugin name(s).
                    'notice_cannot_update'            => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'spring-plant'), // %1$s = plugin name(s).
                    'install_link'                    => _n_noop('Begin installing plugin', 'Begin installing plugins', 'spring-plant'),
                    'activate_link'                   => _n_noop('Begin activating plugin', 'Begin activating plugins', 'spring-plant'),
                    'return'                          => esc_html__('Return to Required Plugins Installer', 'spring-plant'),
                    'plugin_activated'                => esc_html__('Plugin activated successfully.', 'spring-plant'),
                    'complete'                        => esc_html__('All plugins installed and activated successfully. %s', 'spring-plant'), // %s = dashboard link.
                    'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
                )
            );
            tgmpa($plugins, $config);
        }
    }
}