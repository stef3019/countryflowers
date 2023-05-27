<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('G5P_Core_Vc')) {
	final class G5P_Core_Vc
	{
		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			spl_autoload_register(array($this, 'vcAutoload'));
			$this->includeVcShortcode();

		}

		/**
		 * Field auto loader
		 * @param $class
		 */
		public function vcAutoload($class)
		{
			$file_name = preg_replace('/^G5P_Vc_/', '', $class);
			if ($file_name !== $class) {
				$file_name = strtolower($file_name);
				$file_name = str_replace('_', '-', $file_name);
				G5P()->loadFile(G5P()->pluginDir("core/vc/inc//{$file_name}.class.php"));
			}
		}

		public function includeVcShortcode() {
			$this->defaultTemplate()->init();
			$this->autoComplete()->init();
			G5P()->shortcode()->init();
			$this->customize()->init();
		}

		/**
		 * @return G5P_Vc_Custom_Default_Template
		 */
		public function defaultTemplate() {
			return G5P_Vc_Custom_Default_Template::getInstance();
		}

		/**
		 * @return G5P_Vc_Auto_Complete
		 */
		public function autoComplete() {
			return G5P_Vc_Auto_Complete::getInstance();
		}

		/**
		 * @return G5P_Vc_Customize
		 */
		public function customize() {
			return G5P_Vc_Customize::getInstance();
		}


	}
}