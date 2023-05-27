<?php
/**
 * The template for displaying footer.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
/**
 * @hooked - Spring_Plant()->templates()->content_wrapper_end() - 1
 **/
do_action('spring_plant_main_wrapper_content_end');
?>
</div><!-- Close Wrapper Content -->
<?php
/**
 * @hooked - Spring_Plant()->templates()->footer() - 5
 */
do_action('spring_plant_after_page_wrapper_content');
?>
</div><!-- Close Wrapper -->
<?php
/**
 * @hooked - Spring_Plant()->templates()->back_to_top() - 5
 **/
do_action('spring_plant_after_page_wrapper');
?>
<?php wp_footer(); ?>
</body>
</html> <!-- end of site. what a ride! -->

