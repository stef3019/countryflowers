<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="pwbe-processing">
	<p>
		<span class="pwbe-processing-message"><?php _e( 'Processing...', 'pw-bulk-edit' ); ?></span><br />
		<img src="<?php echo plugins_url( 'assets/images/processing.gif', dirname( __FILE__ ) ); ?>" width="220" height="19" />
	</p>
</div>