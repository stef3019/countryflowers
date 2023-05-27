<?php
$single_share_enable = Spring_Plant()->options()->get_single_share_enable();
$single_tag_enable = Spring_Plant()->options()->get_single_tag_enable();
?>
<?php
if ($single_tag_enable === 'on') {
	echo '</div>';
}