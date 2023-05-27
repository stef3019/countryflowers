<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

global $pw_bulk_edit;

if ( $pw_bulk_edit->license->has_activated() && $pw_bulk_edit->license->is_expired() && get_option( 'pwbe_help_minimize_renew_notice', '' ) != 'true' ) {
    ?>
    <div id="pwbe-renew-notice" class="pwbe-filters-help-container" style="margin-bottom: 24px;">
        <h3 style="font-weight: 600; color: red;"><?php _e( 'Your license has expired.', 'pw-bulk-edit' ); ?></h3>
        <p><?php _e( 'You may continue using the plugin with all features enabled, however you will no longer receive new updates and features.', 'pw-bulk-edit' ); ?></p>
        <p>
            <a href="<?php echo $pw_bulk_edit->license->get_renew_url(); ?>" target="_blank" class="button button-primary"><?php _e( 'Renew your license', 'pw-bulk-edit' ); ?></a>
            <span id="pwbe-filters-renew-dismiss" class="button"><?php _e( 'Hide this notice', 'pw-bulk-edit' ); ?></span>
        </p>
    </div>
    <?php
}
