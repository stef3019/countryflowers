<?php
if (!class_exists('G5P_Inc_MetaBox_Portfolio')) {
    class G5P_Inc_MetaBox_Portfolio {
        private static $_instance;
        public static function getInstance() {
            if (self::$_instance == NULL) { self::$_instance = new self(); }
            return self::$_instance;
        }
        public function get_single_portfolio_layout($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_layout', $id); }
        public function get_single_portfolio_gallery_layout($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_layout', $id); }
        public function get_single_portfolio_gallery_image_size($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_image_size', $id); }
        public function get_single_portfolio_gallery_image_ratio($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_image_ratio', $id); }
        public function get_single_portfolio_gallery_image_ratio_custom($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_image_ratio_custom', $id); }
        public function get_single_portfolio_gallery_image_width($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_image_width', $id); }
        public function get_single_portfolio_gallery_columns_gutter($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_columns_gutter', $id); }
        public function get_single_portfolio_gallery_columns($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_columns', $id); }
        public function get_single_portfolio_gallery_columns_md($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_columns_md', $id); }
        public function get_single_portfolio_gallery_columns_sm($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_columns_sm', $id); }
        public function get_single_portfolio_gallery_columns_xs($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_columns_xs', $id); }
        public function get_single_portfolio_gallery_columns_mb($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery_columns_mb', $id); }
        public function get_single_portfolio_custom_link($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_custom_link', $id); }
        public function get_single_portfolio_media_type($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_media_type', $id); }
        public function get_single_portfolio_gallery($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_gallery', $id); }
        public function get_single_portfolio_video($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_video', $id); }
        public function get_portfolio_details_date($id = ''){ return $this->getMetaValue('gsf_spring_portfolio_details_date', $id); }
        public function get_portfolio_details_client($id = ''){ return $this->getMetaValue('gsf_spring_portfolio_details_client', $id); }
        public function get_portfolio_details_type($id = ''){ return $this->getMetaValue('gsf_spring_portfolio_details_type', $id); }
        public function get_portfolio_details_author($id = ''){ return $this->getMetaValue('gsf_spring_portfolio_details_author', $id); }
        public function get_single_portfolio_related_enable($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_enable', $id); }
        public function get_single_portfolio_related_full_width_enable($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_full_width_enable', $id); }
        public function get_single_portfolio_related_algorithm($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_algorithm', $id); }
        public function get_single_portfolio_related_carousel_enable($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_carousel_enable', $id); }
        public function get_single_portfolio_related_per_page($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_per_page', $id); }
        public function get_single_portfolio_related_columns_gutter($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_columns_gutter', $id); }
        public function get_single_portfolio_related_columns($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_columns', $id); }
        public function get_single_portfolio_related_columns_md($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_columns_md', $id); }
        public function get_single_portfolio_related_columns_sm($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_columns_sm', $id); }
        public function get_single_portfolio_related_columns_xs($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_columns_xs', $id); }
        public function get_single_portfolio_related_columns_mb($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_columns_mb', $id); }
        public function get_single_portfolio_related_post_paging($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_post_paging', $id); }
        public function get_single_portfolio_related_animation($id = ''){ return $this->getMetaValue('gsf_spring_single_portfolio_related_animation', $id); }
        public function getMetaValue($meta_key, $id = '') {
            if ($id === '') {
                $id = get_the_ID();
            }

            $value = get_post_meta($id, $meta_key, true);
            if ($value === '') {
                $default = &$this->getDefault();
                if (isset($default[$meta_key])) {
                    $value = $default[$meta_key];
                }
            }
            return $value;
        }


        public function &getDefault() {
            $default = array (
                'gsf_spring_single_portfolio_layout' => '',
                'gsf_spring_single_portfolio_gallery_layout' => 'carousel',
                'gsf_spring_single_portfolio_gallery_image_size' => 'medium',
                'gsf_spring_single_portfolio_gallery_image_ratio' => '1x1',
                'gsf_spring_single_portfolio_gallery_image_ratio_custom' =>
                    array (
                        'width' => '',
                        'height' => '',
                    ),
                'gsf_spring_single_portfolio_gallery_image_width' =>
                    array (
                        'width' => '400',
                        'height' => '',
                    ),
                'gsf_spring_single_portfolio_gallery_columns_gutter' => '10',
                'gsf_spring_single_portfolio_gallery_columns' => '3',
                'gsf_spring_single_portfolio_gallery_columns_md' => '3',
                'gsf_spring_single_portfolio_gallery_columns_sm' => '2',
                'gsf_spring_single_portfolio_gallery_columns_xs' => '2',
                'gsf_spring_single_portfolio_gallery_columns_mb' => '1',
                'gsf_spring_single_portfolio_custom_link' => '',
                'gsf_spring_single_portfolio_media_type' => 'image',
                'gsf_spring_single_portfolio_gallery' => '',
                'gsf_spring_single_portfolio_video' =>
                    array (
                        0 => '',
                    ),
                'gsf_spring_portfolio_details_date' => '',
                'gsf_spring_portfolio_details_client' => '',
                'gsf_spring_portfolio_details_type' => '',
                'gsf_spring_portfolio_details_author' => '',
                'gsf_spring_single_portfolio_related_enable' => '',
                'gsf_spring_single_portfolio_related_full_width_enable' => '',
                'gsf_spring_single_portfolio_related_algorithm' => '',
                'gsf_spring_single_portfolio_related_carousel_enable' => '',
                'gsf_spring_single_portfolio_related_per_page' => '',
                'gsf_spring_single_portfolio_related_columns_gutter' => '',
                'gsf_spring_single_portfolio_related_columns' => '',
                'gsf_spring_single_portfolio_related_columns_md' => '',
                'gsf_spring_single_portfolio_related_columns_sm' => '',
                'gsf_spring_single_portfolio_related_columns_xs' => '',
                'gsf_spring_single_portfolio_related_columns_mb' => '',
                'gsf_spring_single_portfolio_related_post_paging' => '',
                'gsf_spring_single_portfolio_related_animation' => '',
            );
            return $default;
        }
    }
}