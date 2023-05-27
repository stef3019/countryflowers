<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
global $pwbe_sql_builder;

ob_implicit_flush(true);
ob_end_flush();

$products = $pwbe_sql_builder->get_products( $_POST );
$views = PWBE_Views::get();

$product_columns = PWBE_Columns::get();

$hidden_columns = array();
$selected_view = get_option( 'pwbe_selected_view', 'pwbeview_all' );
if ( isset( $views[ $selected_view ] ) ) {
    $hidden_columns = $views[ $selected_view ];
}

if ( function_exists( 'wc_get_price_thousand_separator' ) ) {
    $price_thousand_separator = wc_get_price_thousand_separator();
} else {
    $price_thousand_separator = stripslashes( apply_filters( 'wc_get_price_thousand_separator', get_option( 'woocommerce_price_thousand_sep' ) ) );
}

if ( function_exists( 'wc_get_price_decimal_separator' ) ) {
    $price_decimal_separator = wc_get_price_decimal_separator();
} else {
    $price_decimal_separator = apply_filters( 'wc_get_price_decimal_separator', get_option( 'woocommerce_price_decimal_sep' ) );
    $price_decimal_separator = $price_decimal_separator ? stripslashes( $price_decimal_separator ) : '.';
}

if ( function_exists( 'wc_get_price_decimals' ) ) {
    $price_decimals = wc_get_price_decimals();
} else {
    $price_decimals = absint( apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) ) );
}

?>
<input type="hidden" id="pwbe-price-thousand-separator" value="<?php echo $price_thousand_separator; ?>" />
<input type="hidden" id="pwbe-price-decimal-separator" value="<?php echo $price_decimal_separator; ?>" />
<input type="hidden" id="pwbe-price-decimal-places" value="<?php echo $price_decimals; ?>" />
<div id="pwbe-results-error" class="pwbe-error pwbe-hidden"></div>
<div id="pwbe-results-container">
<?php
    if ( !is_string( $products ) ) {
        if ( PWBE_DB::num_rows( $products ) > 0 ) {
            ?>
            <p class="pwbe-results-buttons">
                <button id="pwbe-product-save-button" class="button button-primary" title="<?php _e( 'Save Products', 'pw-bulk-edit' ); ?>" disabled="disabled"><i class='fa fa-floppy-o fa-fw' aria-hidden='true'></i> <?php _e( 'Save Changes', 'pw-bulk-edit' ); ?></button>
                <!--// <button id="pwbe-product-fix-attributes-button" class="button button-secondary" title="Fix Attributes"><i class='fa fa-wrench fa-fw' aria-hidden='true'></i> Fix Attributes</button> //-->
                <button id="pwbe-product-undo-button" class="button button-secondary pwbe-product-undo-button" title="<?php _e( 'Undo', 'pw-bulk-edit' ); ?>" disabled="disabled"><i class='fa fa-undo fa-fw' aria-hidden='true'></i></button>
                <button id="pwbe-product-redo-button" class="button button-secondary" title="<?php _e( 'Redo', 'pw-bulk-edit' ); ?>" disabled="disabled"><i class='fa fa-repeat fa-fw' aria-hidden='true'></i></button>
                <button id="pwbe-product-discard-button" class="button pwbe-button-red" title="<?php _e( 'Discard All Changes', 'pw-bulk-edit' ); ?>" disabled="disabled"><i class='fa fa-refresh fa-fw' aria-hidden='true'></i></button>
                <span id="pwbe-records-found"></span>
                <span id="pwbe-view-container">
                    <strong><?php _e( 'View', 'pw-bulk-edit' ); ?> </strong>
                    <select id="pwbe-view" name="pwbe_view">
                        <?php
                            foreach( $views as $key => $value ) {
                                $view = htmlspecialchars( $key, ENT_COMPAT );
                                $view_name = $view;

                                if ( $view == 'pwbeview_all' ) {
                                    $view_name = __( 'All Columns', 'pw-bulk-edit' );
                                } else if ( $view == 'pwbeview_default' ) {
                                    $view_name = __( 'Standard Columns', 'pw-bulk-edit' );
                                }

                                echo "<option value=\"$view\" " . selected( $selected_view, $key, false ) . ">$view_name</option>\n";
                            }

                        ?>
                    </select>
                    <span id="pwbe-view-copy" class="pwbe-link pwbe-filter-toolbar-button" title="<?php _e( 'Copy View', 'pw-bulk-edit' ); ?>"><i class="fa fa-files-o fa-fw"></i></span>
                    <span id="pwbe-view-edit" class="pwbe-link pwbe-filter-toolbar-button <?php if ( empty( $selected_view ) || PW_Bulk_Edit::starts_with( 'pwbeview_', $selected_view ) ) { echo 'pwbe-hidden'; } ?>" title="<?php _e( 'Edit View', 'pw-bulk-edit' ); ?>"><i class="fa fa-pencil-square-o fa-fw"></i></span>
                    <span id="pwbe-view-delete" class="pwbe-link pwbe-filter-toolbar-button <?php if ( empty( $selected_view ) || PW_Bulk_Edit::starts_with( 'pwbeview_', $selected_view ) ) { echo 'pwbe-hidden'; } ?>" title="<?php _e( 'Delete View', 'pw-bulk-edit' ); ?>"><i class="fa fa-trash-o fa-fw"></i></span>
                </span>
                <div id="pwbe-auto-create-variations-container" class="pwbe-hidden">
                    <input type="checkbox" id="pwbe-auto-create-variations" name="auto_create_variations" value="1" <?php checked( get_option( 'pwbe_auto_create_variations' ), '1' ); ?>>
                    <label for="pwbe-auto-create-variations"><?php _e( 'Automatically create Variations from Attributes added to Variable Products.', 'pw-bulk-edit' ); ?></label> [<a href="#" onClick="alert('<?php _e( 'When adding Attributes to Variable products, check this box to automatically create the appropriate Variations. This checkbox will have no effect if the product is not a Variable product or if you are removing Attributes.', 'pw-bulk-edit' ); ?>'); return false;">?</a>]
                </div>
                <?php do_action( 'pw_bulk_edit_results_buttons' ); ?>
            </p>
            <form id="pwbe-results-form">
                <div class="pwbe-table pwbe-results-table">
                    <div id="pwbe-header-fixed" class="pwbe-thead"></div>
                    <div id="pwbe-header-results" class="pwbe-thead">
                        <div class="pwbe-tr">
                            <div class="pwbe-td pwbe-results-table-td pwbe-results-table-header-td pwbe-row-checkbox"><input type="checkbox" class="pwbe-checkall" checked="checked" /></div>
                            <div class="pwbe-td pwbe-results-table-td pwbe-results-table-header-td pwbe-view-in-woo">&nbsp;</div>
                            <?php
                                do_action( 'pwbe_before_column_headers' );

                                foreach ( $product_columns as $column ) {
                                    if ( $column['visibility'] != 'none' ) {

                                        if ( in_array( $column['field'], $hidden_columns ) ) {
                                            $hidden = 'pwbe-hidden-column pwbe-hidden';
                                        } else {
                                            $hidden = '';
                                        }

                                        if ( !isset( $column['sortable'] ) ) {
                                            $column['sortable'] = true;
                                        }

                                        if ( !isset( $column['readonly'] ) ) {
                                            $column['readonly'] = false;
                                        }

                                        echo "
                                            <div class='pwbe-td pwbe-results-table-td pwbe-results-table-header-td $hidden' data-field='$column[field]'>
                                                <span class='pwbe-header' data-type='$column[type]' data-field='$column[field]' data-readonly='$column[readonly]' data-sortable='$column[sortable]'>$column[name]</span>&nbsp;";

                                                if ( $_POST['order_by'] == $column['field'] ) {
                                                    if ( empty( $_POST['order_by_desc'] ) ) {
                                                        echo "<i class='fa fa-sort-asc' aria-hidden='true'></i>";
                                                    } else {
                                                        echo "<i class='fa fa-sort-desc' aria-hidden='true'></i>";
                                                    }
                                                }

                                        echo '</div>';
                                    }
                                }

                                do_action( 'pwbe_after_column_headers' );
                            ?>
                        </div>
                    </div>
                    <div class="pwbe-tbody">
                        <?php

                            $max_results = get_option( 'pwbe_max_results', false );
                            if ( false === $max_results || !is_numeric( $max_results ) ) {
                                $max_results = PWBE_MAX_RESULTS;
                            }

                            $attribute_names = array();
                            $attributes = PWBE_Attributes::get_attributes();
                            foreach ( $attributes as $attribute ) {
                                $attribute_names[] = $attribute['slug'];
                            }

                            $taxonomy_list = $attribute_names;
                            $taxonomy_list[] = 'product_tag';
                            $taxonomy_list[] = 'product_cat';
                            $taxonomy_list[] = 'product_brand';
                            $taxonomy_list[] = 'yith_product_brand';

                            if ( class_exists( 'YITH_Vendors' ) ) {
                                $taxonomy_list[] = 'yith_shop_vendor';
                            }

                            $taxonomy_list = apply_filters( 'pwbe_taxonomy_list', $taxonomy_list );

                            $query = $wpdb->prepare("
                                SELECT
                                    DISTINCT
                                    pwbe_products.post_id,
                                    taxonomy.taxonomy AS name,
                                    terms.slug
                                FROM
                                    pwbe_products
                                JOIN
                                    {$wpdb->term_relationships} AS relationships ON (relationships.object_id = pwbe_products.post_id)
                                JOIN
                                    {$wpdb->term_taxonomy} AS taxonomy ON (taxonomy.term_taxonomy_id = relationships.term_taxonomy_id)
                                JOIN
                                    {$wpdb->terms} AS terms ON (terms.term_id = taxonomy.term_id)
                                WHERE
                                    taxonomy.taxonomy IN (" . implode( ', ', array_fill( 0, count( $taxonomy_list ), '%s' ) ) . ")
                                ORDER BY
                                    pwbe_products.post_id,
                                    taxonomy.taxonomy,
                                    taxonomy.term_id
                            ",
                            $taxonomy_list);
                            $taxonomies = $wpdb->get_results($query);

                            $custom_attributes = array();
                            foreach ( PWBE_Attributes::get_custom_attributes() as $attribute_slug => $values ) {
                                foreach ( $values as $value_slug => $product_ids ) {
                                    foreach ( $product_ids as $product_id ) {
                                        $custom_attributes[ $product_id ][ $attribute_slug ][] = $value_slug;
                                    }
                                }
                            }

                            $i = 0;
                            $result_limit_exceeded = false;

                            global $pwbe_product;

                            while ( $pwbe_product = PWBE_DB::fetch_object( $products ) ) {
                                if ( $i > $max_results ) {
                                    $result_limit_exceeded = true;
                                    break;
                                } else {
                                    $i++;
                                }

                                // Must hack/massage the product_shipping_class field a little bit to support variations.
                                if ( $pwbe_product->product_shipping_class == PW_Bulk_Edit::NULL && $pwbe_product->post_id == $pwbe_product->parent_post_id ) {
                                    $pwbe_product->product_shipping_class = '';
                                }

                                $pwbe_product->categories = array();
                                $pwbe_product->tags = array();
                                $pwbe_product->brands = array();
                                $pwbe_product->yith_brands = array();
                                foreach( $attribute_names as $n ) {
                                    $name = trim( $n );

                                    if ( !empty( $name ) ) {
                                        $pwbe_product->{$name} = array();
                                        $pwbe_product->{'attribute_' . $name} = '';
                                        $pwbe_product->{'_default_attribute_' . $name} = '';
                                        $pwbe_product->{'_attribute_visibility_' . $name} = '';
                                        $pwbe_product->{'_attribute_variations_' . $name} = '';
                                    }
                                }

                                if ( $pwbe_product->post_id == $pwbe_product->parent_post_id ) {
                                    foreach ( $taxonomies as $taxonomy ) {
                                        if ( $taxonomy->name == 'product_cat' ) {
                                            if ( $taxonomy->post_id == $pwbe_product->post_id ) {
                                                $pwbe_product->categories[] = $taxonomy->slug;
                                            } else if ( count( $pwbe_product->categories ) > 0 ) {
                                                break;
                                            }
                                        } else if ( $taxonomy->name == 'product_tag' ) {
                                            if ( $taxonomy->post_id == $pwbe_product->post_id ) {
                                                $pwbe_product->tags[] = $taxonomy->slug;
                                            } else if ( count( $pwbe_product->tags ) > 0 ) {
                                                break;
                                            }

                                        } else if ( $taxonomy->name == 'product_brand' ) {
                                            if ( $taxonomy->post_id == $pwbe_product->post_id ) {
                                                $pwbe_product->brands[] = $taxonomy->slug;
                                            } else if ( count( $pwbe_product->brands ) > 0 ) {
                                                break;
                                            }

                                        } else if ( $taxonomy->name == 'yith_product_brand' ) {
                                            if ( $taxonomy->post_id == $pwbe_product->post_id ) {
                                                $pwbe_product->yith_brands[] = $taxonomy->slug;
                                            } else if ( count( $pwbe_product->yith_brands ) > 0 ) {
                                                break;
                                            }
                                        } else {
                                            if ( $taxonomy->post_id == $pwbe_product->post_id ) {
                                                $pwbe_product->{$taxonomy->name}[] = $taxonomy->slug;
                                            } else if ( property_exists( $pwbe_product, $taxonomy->name ) && count( $pwbe_product->{$taxonomy->name} ) > 0 ) {
                                                break;
                                            }
                                        }
                                    }

                                    if ( isset( $custom_attributes[ $pwbe_product->post_id ] ) ) {
                                        foreach ( $custom_attributes[ $pwbe_product->post_id ] as $attribute_slug => $values ) {
                                            foreach ( $values as $value_slug ) {
                                                if ( property_exists( $pwbe_product, $attribute_slug ) ) {
                                                    $pwbe_product->{$attribute_slug}[] = $value_slug;
                                                }
                                            }
                                        }
                                    }
                                }

                                $meta_rows = $wpdb->get_results( $wpdb->prepare( "
                                    SELECT
                                        DISTINCT
                                        postmeta.meta_key AS name,
                                        CASE
                                            WHEN postmeta.meta_key = '_tax_status' THEN COALESCE(NULLIF(postmeta.meta_value, ''), 'taxable')
                                            WHEN postmeta.meta_key = '_manage_stock' THEN COALESCE(NULLIF(postmeta.meta_value, ''), 'no')
                                            WHEN postmeta.meta_key = '_stock' THEN CAST(postmeta.meta_value AS SIGNED)
                                            WHEN postmeta.meta_key = '_backorders' THEN COALESCE(NULLIF(postmeta.meta_value, ''), 'no')
                                            WHEN postmeta.meta_key = '_stock_status' THEN COALESCE(NULLIF(postmeta.meta_value, ''), 'instock')
                                            WHEN postmeta.meta_key = '_variation_description' THEN COALESCE(NULLIF(postmeta.meta_value, ''), '')
                                            WHEN postmeta.meta_key = '_featured' THEN COALESCE(NULLIF(postmeta.meta_value, ''), 'no')
                                            WHEN postmeta.meta_key = '_tax_class' THEN COALESCE(postmeta.meta_value, '" . PW_Bulk_Edit::NULL . "')
                                            WHEN postmeta.meta_key = '_visibility' THEN COALESCE(NULLIF(postmeta.meta_value, ''), '" . apply_filters( 'woocommerce_product_visibility_default' , 'visible' ) . "')
                                            ELSE postmeta.meta_value
                                        END AS value
                                    FROM
                                        {$wpdb->postmeta} AS postmeta
                                    WHERE
                                        postmeta.post_id = %d
                                ", $pwbe_product->post_id ) );

                                foreach ( $meta_rows as $meta ) {
                                    if ( $meta->name == '_default_attributes' ) {
                                        $default_attributes = maybe_unserialize( $meta->value );
                                        if ( is_array( $default_attributes ) ) {
                                            foreach ( $default_attributes as $name => $value ) {
                                                $pwbe_product->{'_default_attribute_' . $name} = $value;
                                            }
                                        }

                                    } else if ( $meta->name == '_product_attributes' ) {
                                        $product_attributes = maybe_unserialize( $meta->value );
                                        if ( is_array( $product_attributes ) ) {
                                            foreach ( $product_attributes as $name => $attribute ) {
                                                $pwbe_product->{'_attribute_visibility_' . $name} = isset( $attribute['is_visible'] ) && $attribute['is_visible'] ? 'yes' : 'no';
                                                $pwbe_product->{'_attribute_variations_' . $name} = isset( $attribute['is_variation'] ) && $attribute['is_variation'] ? 'yes' : 'no';
                                            }
                                        }

                                    } else if ( !empty( $meta->name ) ) {
                                        $pwbe_product->{$meta->name} = $meta->value;
                                    }
                                }

                                if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
                                    $p = wc_get_product( $pwbe_product->post_id );
                                    if ( $p ) {
                                        $pwbe_product->_featured = $p->get_featured() ? 'yes' : 'no';
                                        $pwbe_product->_visibility = $p->get_catalog_visibility();
                                    }
                                }

                                if ( !property_exists( $pwbe_product, '_visibility' ) || empty( $pwbe_product->_visibility ) ) {
                                    $pwbe_product->_visibility = apply_filters( 'woocommerce_product_visibility_default' , 'visible' );
                                }

                                ?>
                                <div class="pwbe-tr pwbe-product-tr pwbe-product-tr-selected <?php if ( $pwbe_product->product_type == 'variation' ) { echo 'pwbe-tr-variation'; } else { echo 'pwbe-tr-product'; } ?>">
                                    <div class="pwbe-td pwbe-results-table-td pwbe-row-checkbox"><input class="pwbe-product-checkbox" id="pwbe-product-<?php echo $pwbe_product->post_id; ?>" name="post[]" type="checkbox" value="<?php echo $pwbe_product->post_id; ?>" checked="checked"></div>
                                    <div class="pwbe-td pwbe-results-table-td pwbe-view-in-woo">
                                        <?php
                                            if ( $pwbe_product->product_type != 'variation' ) {
                                                ?>
                                                <a class="pwbe-view-in-woo-link" target="_blank" title="<?php _e( 'View Product in WooCommerce', 'pw-bulk-edit' ); ?>" href="<?php echo get_edit_post_link( $pwbe_product->post_id, 'edit' ); ?>"><i class="fa fa-external-link fa-fw" aria-hidden="true"></i></a>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                    <?php
                                        do_action( 'pwbe_before_columns' );

                                        $pwbe_product = apply_filters( 'pwbe_results_product_initial', $pwbe_product );

                                        foreach ( $product_columns as $column ) {
                                            if ( $column['visibility'] != 'none' ) {

                                                if ( ! in_array( $column['field'], $hidden_columns ) ) {
                                                    $pwbe_product = apply_filters( 'pwbe_results_product', $pwbe_product, $column );
                                                    echo pwbe_field( $pwbe_product, $column );
                                                }
                                            }
                                        }

                                        do_action( 'pwbe_after_columns' );
                                    ?>
                                </div>
                                <?php
                            }

                            PWBE_DB::free_result( $products );
                        ?>
                        <script>
                            <?php
                                if ( true === $result_limit_exceeded ) {
                                    ?>
                                    jQuery('#pwbe-records-found').html('<?php printf( __( 'Maximum %s records found', 'pw-bulk-edit' ), number_format( $max_results ) ); ?> [<a href="#" onClick="alert(\'<?php _e( 'Go to the Settings page if you would like to increase this limit. NOTE: this limit is in place due to browser limitations. Increasing this value may cause unexpected behavior! Instead, we suggest adding additional filters to lower the number of products found.', 'pw-bulk-edit' ); ?>\'); return false;">?</a>] ');
                                    <?php
                                } else {
                                    ?>
                                    jQuery('#pwbe-records-found').html('<?php printf( __( '%s records found', 'pw-bulk-edit' ), number_format( $i ) ); ?>');
                                    <?php
                                }
                            ?>
                        </script>
                    </div>
                </div>
            </form>
            <div id="pwbe-bulkedit-dialog" class="pwbe-dialog" tabindex="0">
                <div class="pwbe-dialog-heading">
                    <i class="fa fa-database"></i> <?php _e( 'Bulk Edit', 'pw-bulk-edit' ); ?> <span class="pwbe-bulkedit-field-name"></span>
                </div>
                <div class="pwbe-dialog-container">
                    <p>
                        <?php _e( 'The Bulk Editor will make changes to all checked items in the grid.', 'pw-bulk-edit' ); ?>
                    </p>
                    <?php
                        require_once( 'bulk_editors/checkbox.php' );
                        require_once( 'bulk_editors/currency.php' );
                        require_once( 'bulk_editors/date.php' );
                        require_once( 'bulk_editors/image.php' );
                        require_once( 'bulk_editors/multiselect.php' );
                        require_once( 'bulk_editors/number.php' );
                        require_once( 'bulk_editors/select.php' );
                        require_once( 'bulk_editors/text.php' );
                        require_once( 'bulk_editors/textarea.php' );
                    ?>
                    <div class="pwbe-dialog-button-container">
                        <button id="pwbe-bulkedit-dialog-button-apply" class="button button-primary pwbe-dialog-button-apply"><?php _e( 'Apply', 'pw-bulk-edit' ); ?></button>
                        <button id="pwbe-bulkedit-dialog-button-cancel" class="button button-secondary pwbe-dialog-button-cancel"><?php _e( 'Cancel', 'pw-bulk-edit' ); ?></a>
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <h3><?php _e( 'No products found matching the filter criteria.', 'pw-bulk-edit' ); ?> <i class="fa fa-frown-o" aria-hidden="true"></i></h3>
            <?php
        }
    } else {
        ?>
        <div class="pwbe-filter-error-heading"><?php printf( __( 'There was an error while filtering. Please send an email to %s with the following information:', 'pw-bulk-edit' ), '<a href="mailto:us@pimwick.com">us@pimwick.com</a>' ); ?></div>
        <div class="pwbe-filter-error-message"><?php echo $products; ?></div>
        <?php
    }
?>
</div>
<div id="pwbe-edit-view-dialog" class="pwbe-dialog">
    <div class="pwbe-dialog-heading">
        <i class="fa fa-filter"></i> <span class="pwbe-filter-manager-dialog-name"><?php _e( 'Edit View', 'pw-bulk-edit' ); ?></span>
        <a href="#" id="pwbe-edit-view-dialog-button-cancel" class="pwbe-dialog-close-x">X</a>
    </div>
    <div class="pwbe-dialog-container">
        <?php
            require( dirname( __FILE__ ) . '/view_manager/edit.php' );
        ?>
    </div>
</div>
<?php

function pwbe_field( $pwbe_product, $column ) {
    global $pw_bulk_edit;

    $field = $column['field'];
    $field_value = null;
    $input_type = $column['type'];
    $visibility = $column['visibility'];
    $readonly = '';

    if ( isset( $column['readonly'] ) && $column['readonly'] == 'true' ) {
        $readonly = 'pwbe-field-readonly';
    }

    // If this is an array field, then we want the first part of the array as the meta field name.
    if ( strpos( $field, '___' ) !== false ) {
        $array_name = substr( $field, 0, strpos( $field, '___' ) );
        $array_key = substr( $field, strpos( $field, '___' ) + 3 );

        if ( property_exists( $pwbe_product, $array_name ) ) {
            $array_value = apply_filters( 'pwbe_results_decode_array', maybe_unserialize( $pwbe_product->{$array_name} ), $field, $pwbe_product, $array_name ) ;

            if ( $array_name === '_downloadable_files' && is_array( $array_value ) ) {
                $index = substr( $array_key, 0, strpos( $array_key, '___' ) );
                $download_field = substr( $array_key, strpos( $array_key, '___' ) + 3 );
                $keys = array_keys( $array_value );

                if ( count( $array_value ) > $index && is_array( $array_value[ $keys[ $index ] ] ) ) {
                    if ( isset( $array_value[ $keys[ $index ] ][ $download_field ] ) ) {
                        $field_value = $array_value[ $keys[ $index ] ][ $download_field ];
                    }
                }

            } else {
                while ( strpos( $array_key, '___' ) !== false ) {
                    $array_name = substr( $array_key, 0, strpos( $array_key, '___' ) );
                    $array_key = substr( $array_key, strpos( $array_key, '___' ) + 3 );

                    if ( isset( $array_value[ $array_name ] ) ) {
                        $array_value = $array_value[ $array_name ];
                    }
                }

                if ( isset( $array_value[ $array_key ] ) ) {
                    $field_value = $array_value[ $array_key ];
                } else if ( empty( $array_key ) ) {
                    $field_value = $array_value;
                }
            }
        }
    } else if ( property_exists( $pwbe_product, $field ) ) {
        $field_value = $pwbe_product->{$field};
    }

    $display_value = $field_value;
    $additional_input_attributes = '';

    switch ( $input_type ) {
        case 'image':
            if ( isset( $pwbe_product->_thumbnail_id ) ) {
                $display_value = $pw_bulk_edit->get_image_html( $pwbe_product->_thumbnail_id );
            } else {
                $display_value = $pw_bulk_edit->get_image_html( 0 );
            }
        break;

        case 'select':
            $select_options = PWBE_Select_Options::get();
            if ( is_array( $field_value ) ) {
                if ( count( $field_value ) > 0 ) {
                    $field_value = $field_value[0];
                } else {
                    $field_value = '';
                }
            }

            // Blank options with select2 are "placeholders" but for Tax Class blank = Standard so we swap it with a single space for the value.
            if ( '_tax_class' == $field && empty( $field_value ) ) {
                $field_value = ' ';
            }

            if ( isset( $select_options[$field][$field_value] ) && !empty( $select_options[$field][$field_value]['name'] ) ) {
                $display_value = htmlspecialchars( $select_options[$field][$field_value]['name'], ENT_QUOTES );
                $field_value = htmlspecialchars( $field_value, ENT_QUOTES );
            } else {
                $field_value = htmlspecialchars( $field_value, ENT_QUOTES );
                $display_value = 'n/a';
            }
        break;

        case 'multiselect':
            if ( !is_array( $field_value ) ) {
                if ( !empty( $field_value ) ) {
                    $field_value = maybe_unserialize( $field_value );
                }

                if ( !is_array( $field_value ) ) {
                    $field_value = array();
                }
            }

            $display_value = 'Edit (' . count( $field_value ) . ')';
            if ( $field_value && count( $field_value ) > 0 ) {
                $field_value = implode(',', $field_value);
            } else {
                $field_value = '';
            }
        break;

        case 'date':
            if ( !empty( $field_value ) ) {
                if ( function_exists( 'wc_string_to_datetime' ) ) {
                    $display_value = wc_string_to_datetime( date( 'c', $field_value ) )->date( 'Y-m-d' );
                } else {
                    $display_value = date_i18n( 'Y-m-d', $field_value, true );
                }
                $field_value = $display_value;
            } else {
                $display_value = 'n/a';
            }
            $additional_input_attributes = 'maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"';
        break;

        case 'currency':
            $field_value = wc_format_localized_price( $field_value );
            $display_value = wc_format_localized_price( $display_value );

            if ( $display_value === '' ) {
                $display_value = 'n/a';
            }
        break;

        case 'checkbox':
            if ( 'open' == $field_value || 'featured' == $field_value ) {
                $display_value = 'yes';
                $field_value = 'yes';
            } else if ( 'closed' == $field_value ) {
                $display_value = 'no';
                $field_value = 'no';
            }

            if ( !isset( $field_value ) || empty( $field_value ) ) {
                $display_value = 'no';
                $field_value = 'no';
            }

            if ( isset( $column['checked_value'] ) && $field_value == $column['checked_value'] ) {
                $display_value = 'yes';
                $field_value = 'yes';
            }

            if ( isset( $column['unchecked_value'] ) && $field_value == $column['unchecked_value'] ) {
                $display_value = 'no';
                $field_value = 'no';
            }
        break;

        case 'number':
            if ( !isset( $display_value ) || $display_value == '' ) {
                $display_value = 'n/a';
            }
        break;

        default:
            if ( !isset( $display_value ) || empty( $display_value ) ) {
                $display_value = 'n/a';
            }

            if ( !empty( $field_value ) ) {
                $field_value = htmlspecialchars( $field_value, ENT_QUOTES );
                $display_value = htmlspecialchars( $display_value, ENT_QUOTES );
            }
        break;
    }

    // Some fields are always hidden.
    switch ( $pwbe_product->product_type ) {
        case 'variable':
            if ( $visibility != 'parent' && $visibility != 'both' ) {
                $readonly = 'pwbe-field-readonly';
                $field_value = '';
                $display_value = '<div class="pwbe-field-variable-product">' . __( 'Variable product', 'pw-bulk-edit' ) . '</div>';
            }
        break;

        case 'variation':
            if ( $visibility != 'variation' && $visibility != 'parent_variation' && $visibility != 'both' ) {
                $readonly = 'pwbe-field-readonly';
                $field_value = '';

                if ( $field == 'post_title' ) {
                    $display_value = '<div class="pwbe-field-variation" data-post-id="' . $pwbe_product->parent_post_id . '">' . sprintf( __( 'Variation of %s', 'pw-bulk-edit' ), substr( $pwbe_product->post_title, 0, 100 ) ) . '</div>';
                } else if ( $field == 'product_type' ) {
                    $display_value = '<div class="pwbe-field-variable-product">' . __( 'Variation', 'pw-bulk-edit' ) . '</div>';
                } else {
                    $attributes = PWBE_Attributes::get_values();
                    if ( isset( $attributes[ $field ] ) ) {
                        $display_value = '<div class="pwbe-field-variable-product">' . __( 'Variation', 'pw-bulk-edit' ) . '</div>';
                    } else {
                        $display_value = '<div class="pwbe-field-variable-product">' . __( 'Same as parent', 'pw-bulk-edit' ) . '</div>';
                    }
                }
            }
        break;

        default:
            if ( $visibility != 'parent' && $visibility != 'parent_variation' && $visibility != 'both' && $visibility != 'simple' ) {
                $readonly = 'pwbe-field-readonly';
                $field_value = '';
                $display_value = '<div class="pwbe-field-variable-product">' . __( 'Variation field', 'pw-bulk-edit' ) . '</div>';
            }
        break;
    }

    if ( $pwbe_product->product_type == 'variation' && $field == 'post_title' ) {
        $variation = wc_get_product( $pwbe_product->post_id );
        if ( $variation ) {
            $display_value = $variation->get_formatted_name();
        }
    }

    $html = "
        <div class='pwbe-td pwbe-results-table-td pwbe-results-table-cell-td' data-field='$field'>
            <div class='pwbe-field pwbe-field-$field $readonly'>
                <input type='hidden' name='pwbe_field_{$field}_{$pwbe_product->post_id}' value='$field_value' class='pwbe-field-value' data-input-type='$input_type' data-original-value='$field_value' data-field='$field' data-post-id='{$pwbe_product->post_id}' data-parent-post-id='{$pwbe_product->parent_post_id}' data-product-type='{$pwbe_product->product_type}' $additional_input_attributes />
                <div class='pwbe-field-label'>$display_value</div>
            </div>
        </div>
    ";

    return $html;
}
