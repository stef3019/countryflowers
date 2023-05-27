<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !defined( 'YWCFAV_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_YWCFAV' ) ) :

defined( 'PWBE_YWCFAV_VIDEO_COUNT' ) or define( 'PWBE_YWCFAV_VIDEO_COUNT', 1 );
defined( 'PWBE_YWCFAV_AUDIO_COUNT' ) or define( 'PWBE_YWCFAV_AUDIO_COUNT', 1 );

final class PWBE_YWCFAV {

    function __construct() {
        if ( defined( 'YWCFAV_PREMIUM' ) ) {
            add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
            add_filter( 'pwbe_save_array_value', array( $this, 'pwbe_save_array_value' ), 10, 3 );
            add_filter( 'pwbe_product_columns', array( $this, 'premium_product_columns' ) );
        } else {
            add_filter( 'pwbe_product_columns', array( $this, 'product_columns' ) );
        }
    }

    function pwbe_select_options( $select_options ) {
        if ( defined( 'YWCFAV_PREMIUM' ) ) {
            for ( $x = 0; $x < PWBE_YWCFAV_VIDEO_COUNT; $x++ ) {
                $select_options["_ywcfav_video___{$x}___type"]['id']['name'] = __( 'By ID', 'yith-woocommerce-featured-video' );
                $select_options["_ywcfav_video___{$x}___type"]['id']['visibility'] = 'both';

                $select_options["_ywcfav_video___{$x}___type"]['url']['name'] = __( 'By URL', 'yith-woocommerce-featured-video' );
                $select_options["_ywcfav_video___{$x}___type"]['url']['visibility'] = 'both';

                $select_options["_ywcfav_video___{$x}___type"]['embd']['name'] = __( 'By Embedded code', 'yith-woocommerce-featured-video' );
                $select_options["_ywcfav_video___{$x}___type"]['embd']['visibility'] = 'both';

                $select_options["_ywcfav_video___{$x}___type"]['upload']['name'] = __( 'By Upload', 'yith-woocommerce-featured-video' );
                $select_options["_ywcfav_video___{$x}___type"]['upload']['visibility'] = 'both';

                $select_options["_ywcfav_video___{$x}___host"]['youtube']['name'] = __( 'YouTube', 'yith-woocommerce-featured-video' );
                $select_options["_ywcfav_video___{$x}___host"]['youtube']['visibility'] = 'both';

                $select_options["_ywcfav_video___{$x}___host"]['vimeo']['name'] = __( 'Vimeo', 'yith-woocommerce-featured-video' );
                $select_options["_ywcfav_video___{$x}___host"]['vimeo']['visibility'] = 'both';
            }
        }

        return $select_options;
    }

    function pwbe_save_array_value( $array_value, $meta_key, $field ) {
        if ( $meta_key == '_ywcfav_video' || $meta_key == '_ywcfav_audio' ) {
            if ( !isset( $array_value['id'] ) || empty( $array_value['id'] ) ) {
                $array_value['id'] = substr( $meta_key, 1 ) . '_id-' . $this->generate_yith_id();
            }

            if ( !isset( $array_value['thumbn'] ) ) {
                $array_value['thumbn'] = '0';
            }

            if ( !isset( $array_value['featured'] ) ) {
                $array_value['featured'] = 'no';
            }
        }

        return $array_value;
    }

    function premium_product_columns( $columns ) {
        for ( $x = 0; $x < PWBE_YWCFAV_VIDEO_COUNT; $x++ ) {
            $number = ( $x > 0 ) ? ( $x + 1 ) : '';

            $columns[] = array(
                'name' => "Product Video $number Title",
                'type' => 'text',
                'table' => 'meta',
                'field' => "_ywcfav_video___{$x}___name",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $columns[] = array(
                'name' => "Product Video $number Type",
                'type' => 'select',
                'table' => 'meta',
                'field' => "_ywcfav_video___{$x}___type",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $columns[] = array(
                'name' => "Product Video $number Thumbnail ID",
                'type' => 'number',
                'table' => 'meta',
                'field' => "_ywcfav_video___{$x}___thumbn",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $columns[] = array(
                'name' => "Product Video $number Host",
                'type' => 'select',
                'table' => 'meta',
                'field' => "_ywcfav_video___{$x}___host",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $columns[] = array(
                'name' => "Product Video $number Content",
                'type' => 'textarea',
                'table' => 'meta',
                'field' => "_ywcfav_video___{$x}___content",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }


        for ( $x = 0; $x < PWBE_YWCFAV_AUDIO_COUNT; $x++ ) {
            $number = ( $x > 0 ) ? ( $x + 1 ) : '';

            $columns[] = array(
                'name' => "Product Audio $number Title",
                'type' => 'text',
                'table' => 'meta',
                'field' => "_ywcfav_audio___{$x}___name",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $columns[] = array(
                'name' => "Product Audio $number URL",
                'type' => 'text',
                'table' => 'meta',
                'field' => "_ywcfav_audio___{$x}___url",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $columns[] = array(
                'name' => "Product Audio $number Thumbnail ID",
                'type' => 'number',
                'table' => 'meta',
                'field' => "_ywcfav_audio___{$x}___thumbn",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        return $columns;
    }

    function product_columns( $columns ) {
        $columns[] = array(
            'name' => __( 'Featured Video URL', 'yith-woocommerce-featured-video' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_video_url',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        return $columns;
    }

    function generate_yith_id( $length = 11 ) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen( $characters );
        $random_string = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
        }

        return $random_string;
    }
}

new PWBE_YWCFAV();

endif;

?>