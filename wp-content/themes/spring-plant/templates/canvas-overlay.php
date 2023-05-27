<?php
/**
 * The template for displaying canvas-overlay.php
 */
$image_url = Spring_Plant()->themeUrl('assets/images/close.png');
$custom_css = <<<CSS
	.canvas-overlay {
		cursor: url({$image_url}) 15 15, default;
	}
CSS;
Spring_Plant()->custom_css()->addCss($custom_css,'canvas_overlay');

?>
<div class="canvas-overlay"></div>
