<?php
/**
 * The template for displaying post-title.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $post_link
 */
if(!isset($post_link)) {
    $post_link = get_the_permalink();
}
?>
<h4 class="gf-post-title"><a title="<?php the_title() ?>" href="<?php echo esc_url($post_link); ?>" class="gsf-link"><?php the_title() ?></a></h4>
