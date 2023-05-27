<?php
/**
 * The template for displaying social-meta
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$post               = get_post();
$enable_social_meta = Spring_Plant()->options()->get_social_meta_enable();
if ( ! $post || ! is_singular() || class_exists( 'WPSEO_Admin' ) || ( $enable_social_meta !== 'on' ) ) {
	return;
}
$title             = strip_tags( get_the_title() );
$permalink         = get_permalink();
$site_name         = get_bloginfo( 'name' );
$excerpt           = get_the_excerpt();
$content           = get_the_content();
$twitter_author    = Spring_Plant()->options()->get_twitter_author_username();
$googleplus_author = Spring_Plant()->options()->get_googleplus_author();

if ( ! empty( $excerpt ) ) {
	$excerpt = Spring_Plant()->helper()->truncateText( $excerpt, 100 );
}

$logo = Spring_Plant()->options()->get_logo();

$image_url = "";
if ( has_post_thumbnail( $post->ID ) ) {
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
	if ( isset( $thumbnail[0] ) ) {
		$image_url = $thumbnail[0];
	}
} else if ( isset( $logo['url'] ) && ! empty( $logo['url'] ) ) {
	$image_url = $logo['url'];
}
?>
    <!-- Facebook Meta -->
    <meta property="og:title" content="<?php echo esc_attr($title . ' - ' . $site_name) ?>" />
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="<?php echo esc_attr( $permalink ); ?>"/>
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>"/>
    <meta property="og:description" content="<?php echo esc_attr( $excerpt ); ?>">
<?php if ( ! empty( $image_url ) ) : ?>
    <meta property="og:image" content="<?php echo esc_attr( $image_url ); ?>"/>
<?php endif; ?>


    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( $excerpt ); ?>">
<?php if ( ! empty( $twitter_author ) ) : ?>
    <meta name="twitter:site" content="@<?php echo esc_attr( $twitter_author ); ?>">
    <meta name="twitter:creator" content="@<?php echo esc_attr( $twitter_author ); ?>">
<?php endif; ?>

<?php if ( ! empty( $image_url ) ) : ?>
    <meta property="twitter:image:src" content="<?php echo esc_attr( $image_url ); ?>"/>
<?php endif; ?>

<?php if ( function_exists( 'is_product' ) && is_product() ) : ?>
	<?php $product = new WC_Product( $post->ID ); ?>
	<?php if ( is_object( $product ) ): ?>
        <meta property="og:price:amount" content="<?php echo esc_attr( $product->price ); ?>"/>
        <meta property="og:price:currency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>"/>
        <meta name="twitter:data1" content="<?php echo esc_attr( $product->price ); ?>">
        <meta name="twitter:label1" content="Price">
	<?php endif; ?>
<?php endif; ?>

    <!-- Google Authorship and Publisher Markup -->
<?php if ( ! empty( $googleplus_author ) ) : ?>
    <link rel="author" href="<?php echo esc_url("https://plus.google.com/{$googleplus_author}/posts") ?>"/>
    <link rel="publisher" href="<?php echo esc_url("https://plus.google.com/{$googleplus_author}")?>"/>
<?php endif; ?>