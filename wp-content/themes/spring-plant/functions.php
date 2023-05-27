<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
if (!class_exists('Spring_Plant')) {
	class Spring_Plant
	{

		/**
		 * The instance of this object
		 *
		 * @static
		 * @access private
		 * @var null | object
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
			spl_autoload_register(array($this, 'incAutoload'));

			$this->hook()->init();

			$this->custom_css()->init();

			$this->custom_js()->init();

			$this->image_resize()->init();

			$this->requirePlugin()->init();

			$this->includes();

			if (class_exists( 'WooCommerce' )) {
				$this->woocommerce()->init();
			}

			if (class_exists('G5P_Inc_Portfolio')) {
				$this->portfolio()->init();
			}
		}

		private function includes()
		{
			require_once($this->themeDir('inc/theme-functions.php'));
		}



		/**
		 * Get Theme Dir
		 *
		 * @param string $path
		 * @return string
		 */
		public function themeDir($path = '') {

			return trailingslashit(get_template_directory()) . $path;
		}

		/**
		 * Get Theme url
		 * @param string $path
		 * @return string
		 */
		public function themeUrl($path = '') {
			return trailingslashit(get_template_directory_uri()) . $path;
		}


		/**
		 * Register sidebar
		 */
		public function registerSidebar()
		{
			return Spring_Plant_Inc_Register_Sidebar::getInstance();
		}


		/**
		 * Inc library auto loader
		 *
		 * @param $class
		 */
		public function incAutoload($class)
		{
			$file_name = preg_replace('/^Spring_Plant_Inc_/', '', $class);
			if ($file_name !== $class) {
				$file_name = strtolower($file_name);
				$file_name = str_replace('_', '-', $file_name);
                $this->loadFile(Spring_Plant()->themeDir("inc/{$file_name}.class.php"));

			}
		}

        public function loadFile($path) {
            if ( $path && is_readable($path) ) {
                include_once($path);
                return true;
            }
            return false;
        }

		/**
		 * Custom Css Object
		 *
		 * @return Spring_Plant_Inc_Custom_Css
		 */
		public function custom_css()
		{
			return Spring_Plant_Inc_Custom_Css::getInstance();
		}

		/**
		 * Custom Js Object
		 *
		 * @return Spring_Plant_Inc_Custom_Js
		 */
		public function custom_js()
		{
			return Spring_Plant_Inc_Custom_Js::getInstance();
		}

		/**
		 * Breadcrumbs Object
		 *
		 * @return Spring_Plant_Inc_Breadcrumbs|null|object
		 */
		public function breadcrumbs()
		{
			return Spring_Plant_Inc_Breadcrumbs::getInstance();
		}

		/**
		 * Helper Object
		 *
		 * @return Spring_Plant_Inc_Helper|null|object
		 */
		public function helper()
		{
			return Spring_Plant_Inc_Helper::getInstance();
		}

		/**
		 * Template Object
		 *
		 * @return Spring_Plant_Inc_Templates|null|object
		 */
		public function templates()
		{
			return Spring_Plant_Inc_Templates::getInstance();
		}

		/**
		 * Blog Object
		 *
		 * @return Spring_Plant_Inc_Blog|null|object
		 */
		public function blog()
		{
			return Spring_Plant_Inc_Blog::getInstance();
		}

		/**
		 * Ajax Object
		 * @return Spring_Plant_Inc_Ajax|null|object
		 */
		public function ajax()
		{
			return Spring_Plant_Inc_Ajax::getInstance();
		}

		/**
		 * Image Resize
		 * @return G5Plus_Image_Resize|null|object
		 */
		public function image_resize()
		{
			require_once($this->themeDir('inc/libs/class-g5plus-image-resize.php'));
			return G5Plus_Image_Resize::getInstance();
		}

		/**
		 * Query
		 * @return Spring_Plant_Inc_Query|null|object
		 */
		public function query() {
			return Spring_Plant_Inc_Query::getInstance();
		}

		/**
		 * G5Plus Assets
		 *
		 * @return Spring_Plant_Inc_Assets
		 */
		public function assets() {
			return Spring_Plant_Inc_Assets::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Hook
		 */
		public function hook() {
			return Spring_Plant_Inc_Hook::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Options
		 */
		public function options() {
			return Spring_Plant_Inc_Options::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Options_Skin
		 */
		public function optionsSkin() {
			return Spring_Plant_Inc_Options_Skin::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_MetaBox
		 */
		public function metaBox() {
			return Spring_Plant_Inc_MetaBox::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_MetaBox_Post
		 */
		public function metaBoxPost() {
			return Spring_Plant_Inc_MetaBox_Post::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_MetaBox_Portfolio
		 */
		public function metaBoxPortfolio() {
			return Spring_Plant_Inc_MetaBox_Portfolio::getInstance();
		}


		/**
		 * @return Spring_Plant_Inc_MetaBox_Product
		 */
		public function metaBoxProduct() {
			return Spring_Plant_Inc_MetaBox_Product::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Theme_Setup
		 */
		public function themeSetup() {
			return Spring_Plant_Inc_Theme_Setup::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Require_Plugin
		 */
		public function requirePlugin() {
			return Spring_Plant_Inc_Require_Plugin::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Font_Icon
		 */
		public function fontIcons() {
			return Spring_Plant_Inc_Font_Icon::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Term_Meta
		 */
		public function termMeta() {
			return Spring_Plant_Inc_Term_Meta::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_User_Meta
		 */
		public function userMeta() {
			return Spring_Plant_Inc_User_Meta::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Woocommerce
		 */
		public function woocommerce() {
			return Spring_Plant_Inc_Woocommerce::getInstance();
		}

		/**
		 * @return Spring_Plant_Inc_Portfolio
		 */
		public function portfolio() {
			return Spring_Plant_Inc_Portfolio::getInstance();
		}

		public function getMetaPrefix() {
			if (function_exists('G5P')) {
				return G5P()->getMetaPrefix();
			}
			return 'gsf_spring_';
		}
	}

	function Spring_Plant()
	{
		return Spring_Plant::getInstance();
	}

	Spring_Plant()->init();
}


