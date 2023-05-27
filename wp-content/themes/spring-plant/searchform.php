<?php
/**
 * Template for displaying search forms in spring
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) )  ?>">
	<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'spring-plant' ) ?>" value="<?php echo get_search_query() ?>" name="s" />
	<button type="submit" class="search-submit"><?php echo esc_attr_x( 'Search', 'submit button','spring-plant' ) ?> <i class="fa fa-search"></i></button>
</form>
