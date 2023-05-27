<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 10/11/2016
 * Time: 10:25 AM
 * @var $item_active
 * @var $values
 * @var $owl_attributes
 * @var $t_custom_class
 * @var $nav_style
 * @var $dot_style
 */
$class_item_number = 'avatar-active-' . $item_active;
$values = (array)vc_param_group_parse_atts($values); ?>
<div class="gsf-testimonials testimonials-style-03 <?php echo esc_attr($t_custom_class);?>">
	<div class="testimonials-avatar-wrap <?php echo esc_attr($class_item_number) ?>">
		<div class="owl-carousel manual testimonials-avatar-slider clearfix nav-<?php echo esc_attr($nav_style) ?>"
			 data-item-active="<?php echo esc_attr($item_active) ?>" data-owl-options='<?php echo json_encode($owl_attributes); ?>'>
			<?php $item_index = 0; ?>
			<?php foreach ($values as $value):
				$name = isset($value['author_name']) ? $value['author_name'] : '';
				$avatar = isset($value['author_avatar']) ? $value['author_avatar'] : '';
				$image_src='';
				$img_attributes = array();
				if (!empty($avatar)) {
					$image_src =  G5P()->image_resize()->resize(array(
						'image_id' => $avatar,
						'width' => 144,
						'height' => 144
					));
					if (count($image_src) > 0 && !empty($image_src)) {
						$image_src = $image_src['url'];
					}

					if (!empty($name)) {
						$img_attributes[] = sprintf('alt="%s"',esc_attr($name));
                    }
				}


				?>
				<div class="tes-avatar-item" data-index="<?php echo esc_attr($item_index) ?>">
					<?php if (!empty($image_src)): ?>
						<img src="<?php echo esc_url($image_src); ?>" <?php echo join(' ', $img_attributes)?>>
						<?php elseif (empty($image_src)): ?>
						<div class="placeholder"></div>
					<?php endif; ?>
				</div>
				<?php $item_index++; ?>
			<?php endforeach; ?>
		</div>
	</div>
	
	<div class="owl-carousel manual testimonials-quoter-slider clearfix dots-<?php echo esc_attr($dot_style) ?>">
		<?php $item_index_single = 0; ?>
		<?php foreach ($values as $value):
			$name = isset($value['author_name']) ? $value['author_name'] : '';
			$job = isset($value['author_job']) ? $value['author_job'] : '';
			$bio = isset($value['author_bio']) ? $value['author_bio'] : '';
			$url = isset($value['author_link']) ? $value['author_link'] : '';
			$avatar = isset($value['author_avatar']) ? $value['author_avatar'] : '';
			$special_text_bio = isset($value['special_text_bio']) ? $value['special_text_bio'] : '';
			?>
			<div class="testimonial-item">
				<div class="testimonials-content">
					<?php if (!empty($bio)): ?>
						<?php  echo(rawurldecode(base64_decode(strip_tags($bio)))); ?>
					<?php endif; ?>
				</div>
				<?php if (!empty($name) || !empty($job)): ?>
					<div class="author-info clearfix">
						<div class="author-attr">
							<?php if (!empty($name)): ?>
								<h6 class="author-name">
									<?php if (!empty($url)): ?>
									    <a href="<?php echo esc_url($url) ?>">
                                    <?php endif; ?>

                                    <?php echo esc_html($name) ?>

                                    <?php if (!empty($url)): ?>
                                        </a>
                                    <?php endif; ?>
								</h6>
							<?php endif; ?>
							<?php if (!empty($job)): ?>
								<span class="author-job">- <?php echo esc_html($job); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php $item_index_single++; ?>
		<?php endforeach; ?>
	</div>
</div>