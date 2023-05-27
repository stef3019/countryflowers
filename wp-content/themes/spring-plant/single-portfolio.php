<?php
$single_layout = Spring_Plant()->options()->get_single_portfolio_layout();
get_header();
while (have_posts()) : the_post();
    Spring_Plant()->helper()->getTemplate("portfolio/single/layout/{$single_layout}");
endwhile;
get_footer();
