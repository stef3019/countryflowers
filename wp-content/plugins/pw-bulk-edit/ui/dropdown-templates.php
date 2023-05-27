<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Pre-fill some select boxes that can be reused on the grid and other forms.
 *
 */
global $wp_version;

?>
<div class="pwbe-dropdown-templates">
    <?php
        do_action( 'pwbe_before_dropdown_templates' );

        foreach ( PWBE_Select_Options::get() as $field_name => $values ) {
            if ( count( $values ) > 0 ) {
                ?>
                <select class="pwbe-dropdown-template-<?php echo $field_name; ?>" data-select2="<?php echo apply_filters( 'pwbe_select2', true, $field_name ); ?>">
                    <?php
                        foreach ( $values as $value => $option ) {
                            echo "<option value='$value' class='pwbe-dropdown-visibility-$option[visibility]'>$option[name]</option>\n";
                        }
                    ?>
                </select>
                <?php
            }
        }
    ?>

    <select class="pwbe-dropdown-template-categories">
        <?php
            $categories = array();
            if ( version_compare( $wp_version, '4.5', '>=' ) ) {
                $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
            } else {
                $terms = get_terms( 'product_cat', array( 'hide_empty' => false ) );
            }
            if ( !is_wp_error( $terms ) ) {
                $this->sort_terms_hierarchically( $terms, $categories );
                echo $this->hierarchical_select( $categories );
            }
        ?>
    </select>

    <select class="pwbe-dropdown-template-tags">
        <?php
            if ( version_compare( $wp_version, '4.5', '>=' ) ) {
                $tags = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => false ) );
            } else {
                $tags = get_terms( 'product_tag', array( 'hide_empty' => false ) );
            }
            if ( !is_wp_error( $tags ) ) {
                foreach ( $tags as $tag ) {
                    if ( !empty( $tag ) ) {
                        echo "<option value='{$tag->slug}'>{$tag->name}</option>\n";
                    }
                }
            }
        ?>
    </select>

    <?php
        if ( taxonomy_exists( 'product_brand' ) ) {
            ?>
            <select class="pwbe-dropdown-template-brands">
                <?php
                    $brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
                    foreach ( $brands as $brand ) {
                        if ( !empty( $brand ) ) {
                            echo "<option value='{$brand->slug}'>{$brand->name}</option>\n";
                        }
                    }
                ?>
            </select>
            <?php
        }

        if ( taxonomy_exists( 'yith_product_brand' ) ) {
            ?>
            <select class="pwbe-dropdown-template-yith_brands">
                <?php
                    $brands = get_terms( array( 'taxonomy' => 'yith_product_brand', 'hide_empty' => false ) );
                    foreach ( $brands as $brand ) {
                        if ( !empty( $brand ) ) {
                            echo "<option value='{$brand->slug}'>{$brand->name}</option>\n";
                        }
                    }
                ?>
            </select>
            <?php
        }
    ?>

    <select class="pwbe-dropdown-template-statuses">
        <?php
            foreach ( $GLOBALS['wp_post_statuses'] as $key => $post_status ) {
                if ( '1' == $post_status->show_in_admin_status_list ) {
                    echo "<option value='$key'>{$post_status->label}</option>\n";
                }
            }
        ?>
    </select>
    <?php
        do_action( 'pwbe_after_dropdown_templates' );
    ?>
</div>
