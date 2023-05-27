<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 12/28/2017
 * Time: 10:07 AM
 */
?>
<div class="portfolio-single-controls">
    <ul class="d-flex align-items-center">
        <li>
            <?php $prev_portfolio = get_adjacent_post(false, '', true, 'portfolio_cat');
            if($prev_portfolio):?>
                <a href="<?php echo esc_url(get_the_permalink($prev_portfolio->ID)); ?>" class="prev-portfolio gsf-link transition03" title="<?php esc_attr_e('Previous', 'spring-plant') ?>">
                    <i class="fa fa-long-arrow-left"></i> <?php esc_html_e('Previous', 'spring-plant') ?>
                </a>
            <?php else: ?>
                <span class="prev-portfolio disable"><i class="fa fa-long-arrow-left"></i> <?php esc_html_e('Previous', 'spring-plant') ?></span>
            <?php endif; ?>
        </li>
        <li>
            <?php $next_portfolio = get_adjacent_post(false, '', false, 'portfolio_cat');
            if($next_portfolio):?>
                <a href="<?php echo esc_url(get_the_permalink($next_portfolio->ID)); ?>" class="next-portfolio gsf-link transition03" title="<?php esc_attr_e('Next', 'spring-plant') ?>">
                    <?php esc_html_e('Next', 'spring-plant') ?> <i class="fa fa-long-arrow-right"></i>
                </a>
            <?php else: ?>
                <span class="next-portfolio disable"><?php esc_html_e('Next', 'spring-plant') ?> <i class="fa fa-long-arrow-right"></i></span>
            <?php endif; ?>
        </li>
    </ul>
</div>
