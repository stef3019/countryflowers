<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( !class_exists( 'G5P_Core_Custom_Editor' ) ) {
    class G5P_Core_Custom_Editor
    {
        /**
         * The instance of this object
         *
         * @var null|object
         */
        private static $_instance;

        /**
         * Init G5P_Core_Custom_Editor
         *
         * @return G5P_Core_Custom_Editor|null|object
         */
        public static function getInstance()
        {
            if ( self::$_instance == NULL ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function init()
        {
            add_filter('mce_buttons', array($this, 'custom_editor_register_buttons'));
            add_filter('mce_external_plugins', array($this, 'custom_editor_register_tinymce_javascript'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        public function enqueue_scripts()
        {
			wp_enqueue_script(G5P()->assetsHandle('custom-editor'), G5P()->helper()->getAssetUrl('core/custom-editor/assets/js/custom-editor.min.js'), array( 'jquery' ), G5P()->pluginVer(), true);
            wp_localize_script( G5P()->assetsHandle('custom-editor'), 'custom_editor_var',
                array(
                    'menu_name' => esc_html__('Customs', 'spring-framework'),
                    'blockquote_text' => array(
                        esc_html__('Blockquote', 'spring-framework'),
                        esc_html__('Blockquote - Center', 'spring-framework'),
                        is_rtl() ? esc_html__('Blockquote - Right', 'spring-framework') : esc_html__('Blockquote - Left', 'spring-framework'),
                        is_rtl() ? esc_html__('Blockquote - Left', 'spring-framework') : esc_html__('Blockquote - Right', 'spring-framework'),
                    ),
                    'content_padding_text' => array(
                        esc_html__('Content Paddings', 'spring-framework'),
                        esc_html__('Content ⇠', 'spring-framework'),
                        esc_html__('⇢ Content', 'spring-framework'),
                        esc_html__('⇢ Content ⇠', 'spring-framework'),
                        esc_html__('⇢ Content ⇠⇠', 'spring-framework'),
                        esc_html__('⇢⇢ Content ⇠', 'spring-framework'),
                        esc_html__('⇢⇢ Content ⇠⇠', 'spring-framework'),
                        esc_html__('⇢⇢⇢ Content ⇠⇠⇠', 'spring-framework')
                    ),
                    'dropcap_text' => array(
                        esc_html__('Dropcap', 'spring-framework'),
                        esc_html__('Dropcap - Simple', 'spring-framework'),
                        esc_html__('Dropcap - Square', 'spring-framework'),
                        esc_html__('Dropcap - Square Outline', 'spring-framework'),
                        esc_html__('Dropcap - Cirlce', 'spring-framework'),
                        esc_html__('Dropcap - Circle Outline', 'spring-framework')
                    ),
                    'highlighted_text' => array(
                        esc_html__('Highlighted Text', 'spring-framework'),
                        esc_html__('Highlighted Yellow', 'spring-framework'),
                        esc_html__('Highlighted Red', 'spring-framework')
                    ),
                    'column_text' => array(
                        esc_html__('Columns', 'spring-framework'),
                        esc_html__('2 Columns', 'spring-framework'),
                        esc_html__('3 Columns', 'spring-framework'),
                        esc_html__('4 Columns', 'spring-framework')
                    ),
                    'custom_list_text' => array(
                        esc_html__('Custom List', 'spring-framework'),
                        esc_html__('Check List', 'spring-framework'),
                        esc_html__('Star List', 'spring-framework'),
                        esc_html__('Edit List', 'spring-framework'),
                        esc_html__('Folder List', 'spring-framework'),
                        esc_html__('File List', 'spring-framework'),
                        esc_html__('Heart List', 'spring-framework'),
                        esc_html__('Asterisk List', 'spring-framework')
                    ),
                    'divider_text' => array(
                        esc_html__('Drives', 'spring-framework'),
                        esc_html__('Drive Full', 'spring-framework'),
                        esc_html__('Drive Small', 'spring-framework'),
                        esc_html__('Drive Tiny', 'spring-framework'),
                        esc_html__('Drive Large', 'spring-framework')
                    ),
                    'alert_text' => array(
                        esc_html__('Alerts', 'spring-framework'),
                        esc_html__('Alert Simple', 'spring-framework'),
                        esc_html__('Alert Success', 'spring-framework'),
                        esc_html__('Alert Info', 'spring-framework'),
                        esc_html__('Alert Warning', 'spring-framework'),
                        esc_html__('Alert Danger', 'spring-framework'),
                    ),
                    'white_text' => esc_html__('White Text', 'spring-framework')
                )
            );
            wp_enqueue_style(G5P()->assetsHandle('custom-editor'), G5P()->helper()->getAssetUrl('core/custom-editor/assets/css/custom-editor.min.css'), array(), G5P()->pluginVer());
        }

        public function custom_editor_register_buttons( $buttons )
        {
            array_push( $buttons, 'custom_editor', 'separator' );
            return $buttons;
        }

        public function custom_editor_register_tinymce_javascript( $plugin_array )
        {
            $plugin_array['custom_editor'] = G5P()->pluginUrl() . 'core/custom-editor/assets/js/custom-editor-menu.js';
            return $plugin_array;
        }
    }
}