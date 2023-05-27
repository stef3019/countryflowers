
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 10/11/2016
 * Time: 10:25 AM
 * @var $name
 * @var $job
 * @var $bio
 * @var $image_src
 * @var $url
 * @var $image
 */
$bq_image_src = '';
if (!empty($image)) {
	$bq_image_src = wp_get_attachment_image_src($image, 'full');
	if ($bq_image_src && !empty($bq_image_src[0])) {
		$bq_image_src = $bq_image_src[0];
	}
}
$img_attributes = array();
if (!empty($name)) {
	$img_attributes[] = sprintf('alt="%s"',esc_attr($name));
}
?>
<div class="testimonial-item">
    <?php if(!empty($image_src)): ?>
        <div class="author-avatar">
            <img src="<?php echo esc_url($image_src); ?>" <?php echo join(' ', $img_attributes) ?>>
        </div>
    <?php endif; ?>
	<div class="testimonials-content">
		<?php if (!empty($bio)): ?>
			<?php  echo(rawurldecode(base64_decode(strip_tags($bio)))); ?>
		<?php endif; ?>
	</div>
	<?php if (!empty($name) || !empty($job) || !empty($image_src)): ?>
		<div class="author-info clearfix">
			<div class="author-attr">
				<?php if (!empty($name)): ?>
					<h6 class="author-name">
						<?php if (!empty($url)): ?>
						<a href="<?php echo esc_url($url) ?>">
							<?php endif;
							echo esc_attr($name);
							if (!empty($url)): ?>
						</a>
					<?php endif; ?>
					</h6>
				<?php endif; ?>
				<?php if (!empty($job)): ?>
					<span class="author-job"><?php echo esc_html($job); ?></span>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</div>