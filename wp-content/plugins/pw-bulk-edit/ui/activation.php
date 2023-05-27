<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

global $pw_bulk_edit;

?>
<div id="pwbe-activation-main" class="pwbe-activation-main pwbe-hidden">
	<div id="pwbe-activation">
		<h3><?php _e( 'Thank you for your purchase!', 'pw-bulk-edit' ); ?></h3>
		<p><?php _e( 'Enter the license key that was sent to your email address.', 'pw-bulk-edit' ); ?></p>
		<p><?php printf( __( 'If you need assistance email %s.', 'pw-bulk-edit' ), '<a href="mailto:us@pimwick.com">us@pimwick.com</a>' ); ?></p>
		<div id="pwbe-activation-error" class="pwbe-error"><?php echo $pw_bulk_edit->license->error; ?></div>
		<p>
			<form method="post">
				<label for="license-key"><?php _e( 'License Key', 'pw-bulk-edit' ); ?></label>
				<input class="regular-text pwbe-license-key" type="text" name="license-key" required>
				<input type="submit" name="activate-license" value="Activate" class="button button-primary" />
			</form>
		</p>
	</div>
</div>
