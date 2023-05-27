<?php
///**
// * Created by PhpStorm.
// * User: Administrator
// * Date: 10/11/2016
// * Time: 10:25 AM
// * @var $name
// * @var $job
// * @var $bio
// * @var $image_src
// * @var $url
// * @var $index
// */
$index = ($index < 10 ? '#0' : '#') . $index;
$img_attributes = array();
$a_attributes = array();
if (!empty($name)) {
	$img_attributes[] = sprintf('alt="%s"',esc_attr($name));
	$a_attributes[] = sprintf('title="%s"',esc_attr($name));
}


?>
<div class="testimonial-item" data-item-before="<?php echo esc_attr($index); ?>">
    <div class="testimonials-content">
        <?php if (!empty($bio)): ?>
            <?php  echo(rawurldecode(base64_decode(strip_tags($bio)))); ?>
        <?php endif; ?>
    </div>
    <?php if (!empty($name) || !empty($job) || !empty($image_src)): ?>
        <div class="author-info clearfix">
            <div class="d-flex align-items-center">
                <?php if(!empty($image_src)): ?>
                    <div class="author-avatar">
                        <?php if (!empty($url)): ?>
                            <a href="<?php echo esc_url($url) ?>" <?php echo join(' ', $a_attributes) ?>>
                        <?php endif; ?>
                        <img src="<?php echo esc_url($image_src); ?>" <?php echo join(' ', $img_attributes) ?>>
                        <?php if (!empty($url)): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="author-attr">
                    <?php if (!empty($name)): ?>
                        <h6 class="author-name">
                            <?php if (!empty($url)): ?>
                                <a href="<?php echo esc_url($url) ?>" <?php echo join(' ', $a_attributes) ?>>
                            <?php endif;
                                echo esc_attr($name);
                            if (!empty($url)): ?>
                                </a>
                            <?php endif; ?>
                        </h6>
                    <?php endif; ?>
                    <?php if (!empty($job)): ?>
                        <span>- <?php echo esc_html($job); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>