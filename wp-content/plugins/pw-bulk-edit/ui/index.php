<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap" id="pwbe-main">

	<div class="pwbe-title-container">
		<span class="pwbe-pull-right">
			<?php _e( 'Spread the word!', 'pw-bulk-edit' ); ?>
			<div id="share-panel" class="">
				<i data-site="facebook" class="fa fa-facebook-square fa-fw fa-2x pwbe-link pwbe-social-link" title="<?php _e( 'Share on Facebook', 'pw-bulk-edit' ); ?>"></i>
				<i data-site="twitter" class="fa fa-twitter-square fa-fw fa-2x pwbe-link pwbe-social-link" title="<?php _e( 'Share on Twitter', 'pw-bulk-edit' ); ?>"></i>
				<i data-site="google-plus" class="fa fa-google-plus-square fa-fw fa-2x pwbe-link pwbe-social-link" title="<?php _e( 'Share on Google+', 'pw-bulk-edit' ); ?>"></i>
				<i data-site="reddit" class="fa fa-reddit-square fa-fw fa-2x pwbe-link pwbe-social-link" title="<?php _e( 'Share on Reddit', 'pw-bulk-edit' ); ?>"></i>
				<i data-site="tumblr" class="fa fa-tumblr-square fa-fw fa-2x pwbe-link pwbe-social-link" title="<?php _e( 'Share on Tumblr', 'pw-bulk-edit' ); ?>"></i>
				<i data-site="pinterest" class="fa fa-pinterest-square fa-fw fa-2x pwbe-link pwbe-social-link" title="<?php _e( 'Share on Pinterest', 'pw-bulk-edit' ); ?>"></i>
			</div>
		</span>
		<span class="pwbe-title">PW WooCommerce Bulk Edit Pro</span>
		<span class="pwbe-version">v<?php echo $version; ?></span>

		<div>
			by <a href="https://www.pimwick.com" target="_blank" class="pwbe-link">Pimwick, LLC</a>
		</div>
	</div>

	<?php require( 'renew.php' ); ?>

	<?php require( 'dropdown-templates.php' ); ?>

	<?php require( 'filters.php' ); ?>

	<div id="pwbe-message"></div>

	<?php require( 'processing.php' ); ?>

	<div id="pwbe-results">
		<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/results-header.png'; ?>" height="176" width="800" class="pwbe-premium-item">
	</div>
</div>
