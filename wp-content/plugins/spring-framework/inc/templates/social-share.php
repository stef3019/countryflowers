<?php
/**
 * The template for displaying social-share.php
 * @var $page_permalink
 * @var $page_title
 * @var $layout - Accepts 'classic', 'circle', 'square'
 * @var $show_title
 * @var $share_title
 * @var $post_type
 */
if(!isset($share_title)) {
    $share_title = '';
}
$social_share = G5P()->options()->get_social_share();
unset($social_share['sort_order']);
if (count($social_share) === 0) return;
$wrapper_classes = array(
    'gf-social-icon',
    'gf-inline'
);
if (isset($layout) && !empty($layout) && ($layout !== 'classic')) {
    $wrapper_classes[] = "social-icon-{$layout}";
}
if ($page_permalink === '') {
    $page_permalink = get_permalink();
}

if ($page_title === '') {
    $page_title = get_the_title();
}
$post_type = !empty($post_type) ? $post_type : '';
$wrapper_class = implode(' ', array_filter($wrapper_classes));
?>
<div class="gf-social-inner d-flex align-items-center">
    <?php if(!empty($share_title)): ?>
        <span class="gf-share-title"><?php echo wp_kses_post($share_title); ?></span>
    <?php endif; ?>
	<?php if('post' === $post_type): ?>
		<span class="gf-post-share-title"><i class="fa fa-share-alt"></i> <?php esc_html_e('Share this post', 'spring-framework'); ?></span>
	<?php elseif ('product' === $post_type): ?>
		<span class="gf-product-share-title"><?php esc_html_e('Share:', 'spring-framework'); ?></span>
	<?php endif; ?>
    <ul class="<?php echo esc_attr($wrapper_class)?>">
        <?php foreach((array)$social_share as $key => $value) {
            $link = '';
            $icon = '';
            $title = '';
            switch ($key) {
                case 'facebook':
                    $link = "https://www.facebook.com/sharer.php?u=" . urlencode($page_permalink);
                    $icon = 'fa fa-facebook';
                    $title = esc_html__('Facebook', 'spring-framework');
                    break;
                case 'twitter':
                    $by = '';
                    $twitter_author_username = G5P()->options()->get_twitter_author_username();
                    if ($twitter_author_username !== '') {
                        $by = "@{$twitter_author_username}";
                    }
                    $link  = "javascript: window.open('//twitter.com/share?text=" . $page_title . $by . "&url=" . $page_permalink . "','_blank', 'width=900, height=450');";
                    $icon = 'fa fa-twitter';
                    $title = esc_html__('Twitter', 'spring-framework');
                    break;
                case 'google':
                    $link  = "javascript: window.open('//plus.google.com/share?url=" . $page_permalink . "','_blank', 'width=500, height=450');";
                    $icon = 'fa fa-google-plus';
                    $title = esc_html__('Google', 'spring-framework');
                    break;
                case 'linkedin':
                    $link  = "javascript: window.open('//www.linkedin.com/shareArticle?mini=true&url=" . $page_permalink . "&title=" . $page_title . "','_blank', 'width=500, height=450');";
                    $icon = 'fa fa-linkedin';
                    $title = esc_html__('LinkedIn', 'spring-framework');
                    break;
                case 'tumblr':
                    $link  = "javascript: window.open('//www.tumblr.com/share/link?url=" . $page_permalink . "&name=" . $page_title . "','_blank', 'width=500, height=450');";
                    $icon = 'fa fa-tumblr';
                    $title = esc_html__('Tumblr', 'spring-framework');
                    break;
                case 'pinterest':
                    $_img_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                    $link     = "javascript: window.open('//pinterest.com/pin/create/button/?url=" . $page_permalink . '&media=' . (($_img_src === false) ? '' :  $_img_src[0]) . "&description=" . $page_title . "','_blank', 'width=900, height=450');";
                    $icon = 'fa fa-pinterest';
                    $title = esc_html__('Pinterest', 'spring-framework');
                    break;
                case 'email':
                    $link  = "mailto:?subject=" . $page_title . "&body=" . esc_url( $page_permalink );
                    $icon = 'fa fa-envelope';
                    $title = esc_html__('Email', 'spring-framework');
                    break;
                case 'telegram':
                    $link  = "javascript: window.open('https://telegram.me/share/url?url=" . esc_url( $page_permalink ) . "&text=" . $page_title . "','_blank', 'width=900, height=450');";
                    $icon = 'fa fa-send';
                    break;
                case 'whatsapp':
                    $link  = "whatsapp://send?text=" . esc_attr( $page_title . "  \n\n" . esc_url( $page_permalink ) );
                    $icon = 'fa fa-whatsapp';
                    $title = esc_html__('Whats App', 'spring-framework');
                    break;
            }
            ob_start();
            ?>
            <li class="<?php echo esc_attr($key);?>">
                <a class="gsf-link <?php echo esc_attr($layout === 'circle' ? 'gf-hover-circle' : '') ?>" href="<?php echo ($link); ?>" data-toggle="tooltip" title="<?php echo esc_attr($title)?>" target="_blank" rel="nofollow">
                    <i class="<?php echo esc_attr($icon); ?>"></i> <?php if ($show_title === true) { echo esc_html($title);} ?>
                </a>
            </li>
            <?php
            echo ob_get_clean();
        }
        ?>
    </ul>
</div>