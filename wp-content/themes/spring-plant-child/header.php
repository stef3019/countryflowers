<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php if (function_exists('wp_body_open')) {
		wp_body_open();
	} ?>
	<?php
	/**
	 * @hooked - Spring_Plant()->templates()->site_loading() - 5
	 **/
	do_action('spring_plant_before_page_wrapper');
	?>
	<?php
		/**
		 * Color Skin
		 */
		$skin = Spring_Plant()->options()->get_content_skin();
		$skin_classes = Spring_Plant()->helper()->getSkinClass($skin);
		$class  = implode(' ',$skin_classes);
	?>
    
<?php
if($_SERVER['SERVER_NAME'] =='cf.barkode.xyz') {
        echo '<div style="height:50px;background-color:#ff4444;color:black;font-weight:bold;text-align:center;line-height:50px;position: fixed;top: 110px;width: 100%;z-index: 9999;">STAGING</div>';
}?>
	<!-- Open Wrapper -->
	<div id="gf-wrapper" class="<?php echo esc_attr($class)?>">
		<?php
		
		
		/**
		 * @hooked - Spring_Plant()->templates()->top_drawer() - 10
		 * @hooked - Spring_Plant()->templates()->header() - 15
		 **/
		do_action('spring_plant_before_page_wrapper_content');
		?>
		<!-- Open Wrapper Content -->
		<div id="wrapper-content" class="clearfix ">
			<?php
			/**
			 *
			 * @hooked - Spring_Plant()->templates()->content_wrapper_start() - 1
			 **/
			do_action('spring_plant_main_wrapper_content_start');
			?>
