<?php
/**
 * The template for displaying top-bar
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$top_bar_enable = Spring_Plant()->options()->get_mobile_top_bar_enable();
if ($top_bar_enable !== 'on') return;
$content_block = Spring_Plant()->options()->get_mobile_top_bar_content_block();
if (empty($content_block)) return;
?>
<div class="mobile-top-bar">
    <?php echo  Spring_Plant()->helper()->content_block($content_block);?>
</div>
