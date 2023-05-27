<?php
/**
 * The template for displaying post-share.php
 */
$single_share_enable = Spring_Plant()->options()->get_single_share_enable();
$single_tag_enable = Spring_Plant()->options()->get_single_tag_enable();
if ($single_share_enable !== 'on') return;
?>
<?php Spring_Plant()->helper()->getTemplate('single/post-meta')?>
