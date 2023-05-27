<?php
/**
 * The template for displaying search.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$mobile_header_search_enable = Spring_Plant()->options()->get_mobile_header_search_enable();
if ($mobile_header_search_enable !== 'on') return;
?>
<div class="mobile-header-search">
	<div class="container">
		<?php get_search_form(); ?>
	</div>
</div>
