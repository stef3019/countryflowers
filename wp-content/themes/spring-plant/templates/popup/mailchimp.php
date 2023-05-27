<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 13/02/2018
 * Time: 10:37 SA
 */
$mailchimp_popup_enable = Spring_Plant()->options()->get_mailchimp_popup_enable();
$mailchimp_popup_content_block = Spring_Plant()->options()->get_mailchimp_popup_content_block();
if ((isset($_COOKIE['remember_show']) && ($_COOKIE['remember_show'] === "true")) || ($mailchimp_popup_enable !== 'on') || empty($mailchimp_popup_content_block)) return;


$mailchimp_popup_timeout = intval(Spring_Plant()->options()->get_mailchimp_popup_timeout());
if($mailchimp_popup_timeout < 0 ) {
    $mailchimp_popup_timeout = 500;
}
$data_popup_mailchimp = array(
    'data-popup-mailchimp-enable="on"',
    'data-mailchimp-popup-timeout="'.$mailchimp_popup_timeout.'"'
);

$background = Spring_Plant()->options()->get_mailchimp_popup_bg();
$background_attributes = array();
if (isset($background['background_color']) && !empty($background['background_color'])) {
    $background_attributes[] = "background-color: {$background['background_color']} !important";
}

if (isset($background['background_image_url']) && !empty($background['background_image_url'])) {
    $background_repeat = isset($background['background_repeat']) ? $background['background_repeat'] : '';
    $background_position = isset($background['background_position']) ? $background['background_position'] : '';
    $background_size = isset($background['background_size']) ? $background['background_size'] : '';
    $background_attachment = isset($background['background_attachment']) ? $background['background_attachment'] : '';

    $background_attributes[] = "background-image: url('{$background['background_image_url']}')";

    if (!empty($background_repeat)) {
        $background_attributes[] = "background-repeat: {$background_repeat}";
    }

    if (!empty($background_position)) {
        $background_attributes[] = "background-position: {$background_position}";
    }

    if (!empty($background_size)) {
        $background_attributes[] = "background-size: {$background_size}";
    }

    if (!empty($background_attachment)) {
        $background_attributes[] = "background-attachment: {$background_attachment}";
    }
}
$mailchimp_popup_skin = Spring_Plant()->options()->get_mailchimp_popup_skin();
$mailchimp_popup_skin_classes = Spring_Plant()->helper()->getSkinClass($mailchimp_popup_skin);
?>
<div id="gsf-popup-mailchimp-wrapper" class="modal fade <?php echo implode(' ', $mailchimp_popup_skin_classes); ?>" tabindex="-1" role="dialog"
     aria-hidden="true" <?php echo implode(' ',$data_popup_mailchimp) ?>>
    <div class="modal-dialog block-center">
        <div id="gsf-popup-mailchimp-form" class="modal-content block-center-inner" style="<?php echo implode('; ', array_filter($background_attributes));?>">
            <button type="button" class="close" data-dismiss="modal"
                    aria-label="<?php _e('Close', 'spring-plant'); ?>"><i class="flaticon-cross"></i></button>
            <div class="modal-header">
                <?php echo Spring_Plant()->helper()->content_block($mailchimp_popup_content_block); ?>
            </div>
            <div class="modal-footer">
                <input id="remember-show" type="checkbox" name="remember-show"/>
                <label for="remember-show"><?php _e("Don't show this popup again", 'spring-plant') ?></label>
            </div>
        </div>
    </div>
</div>
