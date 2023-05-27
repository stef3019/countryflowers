<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 9/20/2017
 * Time: 10:32 AM
 */
?>
<div class="gf-single-portfolio-wrap clearfix">
	<div id="post-<?php the_ID(); ?>" <?php post_class('portfolio-single clearfix row layout-4'); ?>>
		<div class="gf-portfolio-content col-md-4 gf-sticky">
            <div class="portfolio-item-category">
                <?php the_terms(get_the_ID(), 'portfolio_cat'); ?>
            </div>
			<?php Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-title') ?>
			<div class="gf-entry-content clearfix ">
				<?php
				the_content();
				?>
			</div>
            <?php
            wp_link_pages(array(
                'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'spring-plant') . '</span>',
                'after'       => '</div>',
                'link_before' => '<span class="page-link">',
                'link_after'  => '</span>',
            ));
            ?>
			<div class="gf-portfolio-meta-wrap">
				<?php Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-meta') ?>
			</div>
		</div>
		<div class="portfolio-gallery-content col-md-8 gf-sticky">
			<?php Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-gallery'); ?>
		</div>
	</div>
	<?php
	/**
	 * @hooked - portfolio_related - 10
	 *
	 **/
	do_action('spring_plant_after_single_portfolio')
	?>
</div>