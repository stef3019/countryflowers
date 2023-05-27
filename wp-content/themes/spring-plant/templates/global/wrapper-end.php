<?php
/**
 * The template for displaying wrapper-end
 */
$content_full_width = Spring_Plant()->options()->get_content_full_width();
?>
			</div> <!-- End Primary Content Inner -->
			<?php get_sidebar(); ?>
		</div> <!-- End Primary Content Row -->
	<?php if ($content_full_width !== 'on'): ?>
	</div> <!-- End Primary Content Container -->
	<?php endif; ?>
	<?php
	/**
	 * * @hooked - woocommerce_output_upsells_products - 15
	 * @hooked - woocommerce_output_related_products - 20
	 
	 **/
	do_action('spring_plant_after_main_content');
	?>
</div> <!-- End Primary Content Wrapper -->
