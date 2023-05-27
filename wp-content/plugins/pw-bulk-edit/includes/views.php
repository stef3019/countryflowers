<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PWBE_Views' ) ) :

final class PWBE_Views {

	public static function get() {
		$views = array(
			'pwbeview_all' => array(),
			'pwbeview_default' => array()
		);

		$columns = PWBE_Columns::get();
		foreach( $columns as $column ) {
			if ( isset( $column['views'] ) ) {
				if ( !in_array( 'all', $column['views'] ) ) {
					$views['pwbeview_all'][] = $column['field'];
				}

				if ( !in_array( 'standard', $column['views'] ) ) {
					$views['pwbeview_default'][] = $column['field'];
				}
			}
		}

		$saved_views_string = get_option( 'pwbe_views' );
		$saved_views = maybe_unserialize( $saved_views_string );

		if ( !empty( $saved_views ) && is_array( $saved_views ) ) {
			foreach( $saved_views as $key => $view ) {
				$saved_views[ $key ] = json_decode( $view );
			}

			$views = array_merge( $views, $saved_views );
		}

		return apply_filters( 'pwbe_views', $views );
	}
}

endif;

?>