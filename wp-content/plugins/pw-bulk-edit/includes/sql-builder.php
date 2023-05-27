<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PWBE_SQL_Builder' ) ) :

final class PWBE_SQL_Builder {

    private $parent_only_filters = array();
    private $variation_only_filters = array();

    public function get_products( $post ) {
        global $wpdb;

        $wpdb->show_errors();

        $common_fields = "
            post.ID AS post_id,
            post.post_content AS post_content,
            post.post_excerpt AS post_excerpt,
            post.post_status AS post_status,
            post.comment_status AS comment_status,
            post.menu_order AS menu_order,
            post.post_modified AS post_modified,
            post.post_date AS post_date,
            parent.ID AS parent_post_id,
            parent.post_title AS post_title,
            parent.post_name AS post_name,
            COALESCE((
                SELECT
                    t.slug
                FROM
                    {$wpdb->term_relationships} AS r
                JOIN
                    {$wpdb->term_taxonomy} AS tax ON (tax.term_taxonomy_id = r.term_taxonomy_id)
                JOIN
                    {$wpdb->terms} AS t ON (t.term_id = tax.term_id)
                WHERE
                    r.object_id = post.ID
                    AND tax.taxonomy = 'product_shipping_class'
                LIMIT 1
            ), '" . PW_Bulk_Edit::NULL . "') AS product_shipping_class,

            meta__sku.meta_value AS _sku,
            meta__regular_price.meta_value AS _regular_price,
            meta__sale_price.meta_value AS _sale_price
        ";

        $common_joins = "
            LEFT JOIN
                {$wpdb->postmeta} AS meta__sku ON (meta__sku.post_id = post.ID AND meta__sku.meta_key = '_sku')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__regular_price ON (meta__regular_price.post_id = post.ID AND meta__regular_price.meta_key = '_regular_price')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__sale_price ON (meta__sale_price.post_id = post.ID AND meta__sale_price.meta_key = '_sale_price')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__stock ON (meta__stock.post_id = post.ID AND meta__stock.meta_key = '_stock')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__stock_status ON (meta__stock_status.post_id = post.ID AND meta__stock_status.meta_key = '_stock_status')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__tax_class ON (meta__tax_class.post_id = post.ID AND meta__tax_class.meta_key = '_tax_class')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__tax_status ON (meta__tax_status.post_id = post.ID AND meta__tax_status.meta_key = '_tax_status')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__manage_stock ON (meta__manage_stock.post_id = post.ID AND meta__manage_stock.meta_key = '_manage_stock')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__thumbnail_id ON (meta__thumbnail_id.post_id = post.ID AND meta__thumbnail_id.meta_key = '_thumbnail_id')
            LEFT JOIN
                {$wpdb->postmeta} AS variation_description ON (variation_description.post_id = post.ID AND variation_description.meta_key = '_variation_description')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__length ON (meta__length.post_id = post.ID AND meta__length.meta_key = '_length')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__width ON (meta__width.post_id = post.ID AND meta__width.meta_key = '_width')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__height ON (meta__height.post_id = post.ID AND meta__height.meta_key = '_height')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__weight ON (meta__weight.post_id = post.ID AND meta__weight.meta_key = '_weight')
            -- featured_placeholder
        ";

        if ( !empty( $post['order_by'] ) ) {
            $column = PWBE_Columns::get_by_field( $post['order_by'] );
            if ( !empty( $column ) ) {
                if ( $column['field'] == '_visibility' ) {
                    $common_fields .= ",
                        visibility_exclude_from_catalog.product_id AS `visibility_exclude_from_catalog_product_id`,
                        visibility_exclude_from_search.product_id AS `visibility_exclude_from_search_product_id`
                    ";
                    $common_joins = $this->maybe_add_catalog_visibility_joins( $common_joins );

                } else if ( $column['table'] == 'meta' && !in_array( $column['field'], array( '_sku', '_regular_price', '_sale_price' ) ) ) {
                    if ( $column['field'] == '_featured' && PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
                        $common_fields .= "
                            , CASE WHEN featured_products.product_id IS NOT NULL THEN 'yes' ELSE 'no' END AS `is_featured_product`
                        ";
                        $common_joins = $this->maybe_add_featured_joins( $common_joins );

                    } else {
                        $common_fields .= '
                            , `meta_' . $column['field'] . '`.meta_value AS `' . $column['field'] . '`
                        ';

                        if ( !in_array( $column['field'], array( '_thumbnail_id', '_stock', '_stock_status', '_tax_class', '_tax_status', '_manage_stock', '_length', '_width', '_height', '_weight', '_featured' ) ) ) {
                            $common_joins .= '
                                LEFT JOIN
                                    ' . $wpdb->postmeta . ' AS `meta_' . $column['field'] . '` ON (`meta_' . $column['field'] . '`.post_id = post.ID AND `meta_' . $column['field'] . '`.meta_key = \'' . $column['field'] . '\')
                            ';
                        }
                    }
                }
            }
        }

        $this->parent_only_filters = apply_filters( 'pwbe_parent_only_filters', array( 'tax_status', 'catalog_visibility' ) );
        $this->variation_only_filters = apply_filters( 'pwbe_variation_only_filters', array( ) );

        $where_products = $this->build_common_sql( 'products', '-0', $post['main_group_type'], $post, $common_fields, $common_joins );
        $where_variations = $this->build_common_sql( 'variations', '-0', $post['main_group_type'], $post, $common_fields, $common_joins );

        $common_fields = apply_filters( 'pwbe_common_fields', $common_fields );
        $common_joins = apply_filters( 'pwbe_common_joins', $common_joins );
        $where_products = apply_filters( 'pwbe_where_products', $where_products );
        $where_variations = apply_filters( 'pwbe_where_variations', $where_variations );

        @set_time_limit( 0 );

        $wpdb->query("SET SQL_BIG_SELECTS=1");

        $wpdb->query("DROP TABLE IF EXISTS pwbe_variations");
        $wpdb->query("CREATE TEMPORARY TABLE pwbe_variations (post_id BIGINT(20) UNSIGNED, parent_post_id BIGINT(20) UNSIGNED, PRIMARY KEY (post_id) )");

        $variations_sql = "
            INSERT INTO pwbe_variations
                SELECT
                    post.ID AS post_id,
                    MAX(parent.ID) AS parent_post_id
                FROM
                    {$wpdb->posts} AS post
                JOIN
                    {$wpdb->posts} AS parent ON (parent.ID = post.post_parent)
                $common_joins
                WHERE
                    post.post_type = 'product_variation'
                    AND ($where_variations)
        ";

        if ( PWBE_PREFILTER_VARIATIONS ) {
            $variations_sql .= "
                    AND (
                        SELECT
                            terms.slug
                        FROM
                            {$wpdb->term_relationships} AS term_relationships
                        JOIN
                            {$wpdb->term_taxonomy} AS term_taxonomy ON (term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id)
                        JOIN
                            {$wpdb->terms} AS terms ON (terms.term_id = term_taxonomy.term_id)
                        WHERE
                            term_relationships.object_id = post.post_parent
                            AND term_taxonomy.taxonomy = 'product_type'
                        LIMIT 1
                    ) IN ( " . $this->variable_product_types() . " )
            ";
        }

        $variations_sql .= "
               GROUP BY
                    post.ID
        ";

        $wpdb->query( $variations_sql );

        $wpdb->query("DROP TABLE IF EXISTS pwbe_products");
        $wpdb->query("CREATE TEMPORARY TABLE pwbe_products (post_id BIGINT(20) UNSIGNED, PRIMARY KEY (post_id) )");
        $wpdb->query("
            INSERT INTO pwbe_products
                SELECT
                    DISTINCT
                    post.ID AS post_id
                FROM
                    {$wpdb->posts} AS post
                JOIN
                    {$wpdb->posts} AS parent ON (parent.ID = post.ID)
                $common_joins
                WHERE
                    post.post_type = 'product'
                    AND (
                        ($where_products)
                        OR post.ID IN (SELECT parent_post_id FROM pwbe_variations)
                    )
        ");

        if ( isset( $_POST['show_all_variations'] ) ) {
            // This is to prevent "duplicate primary key" error messages. MySQL won't let you
            // select from the temporary table that you're inserting into and the variation may
            // already be present.
            $wpdb->query("CREATE TEMPORARY TABLE pwbe_variations2 LIKE pwbe_variations");
            $wpdb->query("INSERT INTO pwbe_variations2 SELECT * FROM pwbe_variations");

            $wpdb->query("
                INSERT INTO pwbe_variations
                    SELECT
                        post.ID AS post_id,
                        MAX(parent.post_id) AS parent_post_id
                    FROM
                        pwbe_products AS parent
                    JOIN
                        {$wpdb->posts} AS post ON (post.post_parent = parent.post_id AND post.post_type = 'product_variation')
                    WHERE
                        NOT EXISTS (SELECT 1 FROM pwbe_variations2 WHERE post_id = post.ID)
                    GROUP BY
                        post.ID
            ");
        }

        $sql = "
            SELECT
                DISTINCT
                (
                    SELECT
                        terms.slug
                    FROM
                        {$wpdb->term_relationships} AS term_relationships
                    JOIN
                        {$wpdb->term_taxonomy} AS term_taxonomy ON (term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id)
                    JOIN
                        {$wpdb->terms} AS terms ON (terms.term_id = term_taxonomy.term_id)
                    WHERE
                        term_relationships.object_id = post.ID
                        AND term_taxonomy.taxonomy = 'product_type'
                    LIMIT 1
                ) AS product_type,
                $common_fields
            FROM
                pwbe_products
            JOIN
                {$wpdb->posts} AS post ON (post.ID = pwbe_products.post_id)
            JOIN
                {$wpdb->posts} AS parent ON (parent.ID = post.ID)
            $common_joins
            WHERE
                post.post_type = 'product'

            UNION ALL

            SELECT
                DISTINCT
                'variation' AS product_type,
                $common_fields
            FROM
                pwbe_variations
            JOIN
                {$wpdb->posts} AS post ON (post.ID = pwbe_variations.post_id)
            JOIN
                {$wpdb->posts} AS parent ON (parent.ID = post.post_parent)
            $common_joins
            WHERE
                post.post_type = 'product_variation'
                AND (
                    SELECT
                        terms.slug
                    FROM
                        {$wpdb->term_relationships} AS term_relationships
                    JOIN
                        {$wpdb->term_taxonomy} AS term_taxonomy ON (term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id)
                    JOIN
                        {$wpdb->terms} AS terms ON (terms.term_id = term_taxonomy.term_id)
                    WHERE
                        term_relationships.object_id = post.post_parent
                        AND term_taxonomy.taxonomy = 'product_type'
                    LIMIT 1
                ) IN ( " . $this->variable_product_types() . " )

            ORDER BY
                " . $this->build_order_by( $post ) . "
        ";
        $products = PWBE_DB::query( $sql );

        if ( $products !== false ) {
            return $products;
        } else {
            return 'MySQL Error: ' . PWBE_DB::error();
        }
    }

    public function build_common_sql( $type, $suffix, $group_type, $fields, &$sql_fields, &$sql_joins ) {
        global $wpdb;

        $sql_where = "(";

        // Inside each group, loop through the nested statements.
        for ($row_index = 0; $row_index < count( $fields['row'] ); $row_index++ ) {

            $field_name = '';
            $filter_type = '';
            $field_value = '';
            $field_value2 = '';

            if ( isset ( $fields[$row_index . 'filter_name' . $suffix] ) ) {
                $field_name = $fields[$row_index . 'filter_name' . $suffix];
            }

            if ( isset ( $fields[$row_index . 'filter_type' . $suffix] ) ) {
                $filter_type = $fields[$row_index . 'filter_type' . $suffix];
            }

            // Value is either filter_value or filter_select.
            if ( isset( $fields[$row_index . 'filter_value' . $suffix] ) ) {
                $field_value = $fields[$row_index . 'filter_value' . $suffix];

                // Value2 is optional
                if ( isset( $fields[$row_index . 'filter_value2' . $suffix] ) ) {
                    $field_value2 = $fields[$row_index . 'filter_value2' . $suffix];
                }

            } else if ( isset( $fields[$row_index . 'filter_select' . $suffix] ) ) {
                $field_value = $fields[$row_index . 'filter_select' . $suffix];
            }

            // Only filter the columns for the specified $type.
            if ( ( 'products' === $type && in_array( $field_name, $this->variation_only_filters ) ) || ( 'variations' === $type && in_array( $field_name, $this->parent_only_filters ) ) ) {


                // For parent-only filters, have to check the Parent product for the $field_name value.

                if ( $group_type == 'pwbe_and' ) {
                    $row_sql = ' 1 != 1 ';
                } else if ( $group_type == 'pwbe_or' ) {
                    $row_sql = ' 1 = 1  ';
                }

            } else {
                switch ( $field_name ) {
                    case 'pwbe_and':
                    case 'pwbe_or':
                        $row_sql = $this->build_common_sql( $type, "$suffix-$row_index", $field_name, $fields, $sql_fields, $sql_joins );
                    break;

                    case 'catalog_visibility':
                        $row_sql = $this->catalog_visibility_search( $filter_type, $field_value, $sql_joins );
                    break;

                    case 'thumbnail_id':
                        $row_sql = $this->image_search( 'meta__thumbnail_id.meta_value', $filter_type, $field_value );
                    break;

                    case 'categories':
                        $row_sql = $this->taxonomy_search( 'product_cat', $filter_type, $field_value );
                    break;

                    case 'yith_shop_vendor':
                        $row_sql = $this->taxonomy_search( 'yith_shop_vendor', $filter_type, $field_value );
                    break;

                    case 'tags':
                        $row_sql = $this->taxonomy_search( 'product_tag', $filter_type, $field_value );
                    break;

                    case 'product_brand':
                        $row_sql = $this->taxonomy_search( 'product_brand', $filter_type, $field_value );
                    break;

                    case 'yith_product_brand':
                        $row_sql = $this->taxonomy_search( 'yith_product_brand', $filter_type, $field_value );
                    break;

                    case 'post_status':
                        $row_sql = $this->multiselect_search( 'post.post_status', $filter_type, $field_value );
                    break;

                    case 'stock_status':
                        $row_sql = $this->multiselect_search( 'meta__stock_status.meta_value', $filter_type, $field_value );
                    break;

                    case 'stock_quantity':
                        $row_sql = $this->numeric_search( 'meta__stock.meta_value', $filter_type, $field_value, $field_value2 );
                    break;

                    case 'post_content':
                        $row_sql = $this->string_search( 'parent.post_content', $filter_type, $field_value );
                    break;

                    case 'post_title':
                        $row_sql = $this->string_search( 'parent.post_title', $filter_type, $field_value );
                    break;

                    case 'regular_price':
                        $row_sql = $this->numeric_search( 'meta__regular_price.meta_value', $filter_type, $field_value, $field_value2 );
                    break;

                    case 'product_type':
                        $row_sql = $this->product_type_search( $filter_type, $field_value );
                    break;

                    case 'sale_price':
                        $row_sql = $this->numeric_search( 'meta__sale_price.meta_value', $filter_type, $field_value, $field_value2 );
                    break;

                    case 'post_excerpt':
                        $row_sql = $this->string_search( 'parent.post_excerpt', $filter_type, $field_value );
                    break;

                    case 'sku':
                        $row_sql = $this->string_search( 'meta__sku.meta_value', $filter_type, $field_value );
                    break;

                    case 'post_name':
                        $row_sql = $this->string_search( 'parent.post_name', $filter_type, $field_value );
                    break;

                    case 'variation_description':
                        $row_sql = $this->string_search( 'variation_description.meta_value', $filter_type, $field_value );
                    break;

                    case 'tax_class':
                        $row_sql = $this->multiselect_search( 'meta__tax_class.meta_value', $filter_type, $field_value );
                    break;

                    case 'tax_status':
                        $row_sql = $this->multiselect_search( 'meta__tax_status.meta_value', $filter_type, $field_value );
                    break;

                    case 'meta__length':
                    case 'meta__width':
                    case 'meta__height':
                    case 'meta__weight':
                        $row_sql = $this->numeric_search( $field_name . '.meta_value', $filter_type, $field_value, $field_value2 );
                    break;

                    case 'meta__featured':
                        $sql_joins = $this->maybe_add_featured_joins( $sql_joins );

                        if ( $filter_type == 'is checked' ) {
                            $row_sql = "featured_products.product_id IS NOT NULL";
                        } else {
                            $row_sql = "featured_products.product_id IS NULL";
                        }
                    break;

                    default:
                        $row_sql = apply_filters( 'pwbe_search_row_sql', '', $this, $field_name, $filter_type, $field_value, $field_value2 );
                        if ( empty( $row_sql ) ) {
                            if ( $filter_type == 'is checked' || $filter_type == 'is not checked' ) {
                                $row_sql = $this->boolean_search( "`{$field_name}`.meta_value", $filter_type, $field_value );

                            } else {
                                if ( 'product_shipping_class' === $field_name || PW_Bulk_Edit::starts_with( 'pa_', $field_name ) ) {
                                    $row_sql = $this->attributes_search( $field_name, $filter_type, $field_value );

                                } else {
                                    $products = PWBE_Attributes::get_custom_attributes();
                                    if ( isset( $products[ $field_name ] ) ) {
                                        // Custom Attribute
                                        $row_sql = $this->custom_attributes_search( $field_name, $filter_type, $field_value );

                                    } else {
                                        if ( $group_type == 'pwbe_and' ) {
                                            $row_sql = ' 1 = 1 ';

                                        } else if ( $group_type == 'pwbe_or' ) {
                                            $row_sql = ' 1 != 1  ';

                                        }
                                    }
                                }
                            }
                        }
                    break;
                }
            }

            $sql_where .= apply_filters( 'pwbe_where_clause', $row_sql, $field_name, $filter_type, $field_value, $field_value2, $group_type );

            if ( $group_type == 'pwbe_and' ) {
                $sql_where .= " AND ";

            } else if ( $group_type == 'pwbe_or' ) {
                $sql_where .= " OR  ";
            }
        }

        // Yank the trailing AND/OR.
        $sql_where = substr( $sql_where, 0, -5 );

        $sql_where .= ") ";

        return $sql_where;
    }

    public function string_search( $field_name, $filter_type, $value ) {
        global $wpdb;

        switch( $filter_type ) {
            case 'is':
                return $wpdb->prepare("$field_name = %s", $value);
            break;

            case 'is not':
                return $wpdb->prepare("$field_name != %s", $value);
            break;

            case 'contains':
                return $wpdb->prepare("$field_name LIKE %s", '%' . str_replace( '_', '\_', $value ) . '%');
            break;

            case 'does not contain':
                return $wpdb->prepare("$field_name NOT LIKE %s", '%' . str_replace( '_', '\_', $value ) . '%');
            break;

            case 'begins with':
                return $wpdb->prepare("$field_name LIKE %s", str_replace( '_', '\_', $value ) . '%');
            break;

            case 'ends with':
                return $wpdb->prepare("$field_name LIKE %s", '%' . str_replace( '_', '\_', $value ) );
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is not empty' ) ? 'NOT' : '';
                return "NULLIF(TRIM($field_name), '') IS $negator NULL";
            break;
        }
    }

    public function numeric_search( $field_name, $filter_type, $value, $value2 ) {
        global $wpdb;

        //$field_sql = "$field_name IS NOT NULL AND $field_name != '' AND CAST($field_name AS DECIMAL(12, 2))";
        $field_sql = "$field_name IS NOT NULL AND $field_name != '' AND $field_name";

        switch( $filter_type ) {
            case 'is':
                return $wpdb->prepare("$field_sql = %f", $value);
            break;

            case 'is not':
                return $wpdb->prepare("$field_sql != %f", $value);
            break;

            case 'is greater than':
                return $wpdb->prepare("$field_sql > %f", $value);
            break;

            case 'is less than':
                return $wpdb->prepare("$field_sql < %f", $value);
            break;

            case 'is in the range':
                return $wpdb->prepare("($field_sql >= %f AND CAST($field_name AS DECIMAL(12, 2)) <= %f)", $value, $value2);
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is not empty' ) ? 'NOT' : '';
                $sql = "NULLIF(TRIM($field_name), '') IS $negator NULL";

                // Regular Price and Sale Price will always be empty for Variable products so we need to account for that here.
                if ( in_array( $field_name, array( 'meta__regular_price.meta_value', 'meta__sale_price.meta_value' ) ) ) {
                    $sql = "($sql AND NOT EXISTS (SELECT 1 FROM {$wpdb->posts} AS x WHERE x.post_parent = post.ID AND x.post_type = 'product_variation'))";
                }

                return $sql;
            break;
        }
    }

    public function taxonomy_search( $taxonomy, $filter_type, $values ) {
        global $wpdb;

        if ( !is_array( $values ) ) {
            $values = array();
        }

        $placeholders = implode( ', ', array_fill( 0, count( $values ), '%s' ) );

        if ( !empty( $values ) ) {
            array_unshift( $values, $taxonomy );
        }

        switch( $filter_type ) {
            case 'is any of':
            case 'is none of':
                $negator = ( $filter_type == 'is none of' ) ? 'NOT' : '';
                return $wpdb->prepare("$negator EXISTS (SELECT 1 FROM {$wpdb->term_taxonomy} AS tax JOIN {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id) JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id) WHERE tax.taxonomy = %s AND r.object_id = parent.ID AND t.slug IN ($placeholders))", $values);
            break;

            case 'is all of':
                $sql = '(';
                $sql .= $wpdb->prepare("(SELECT COUNT(*) FROM {$wpdb->term_taxonomy} AS tax JOIN {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id) JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id) WHERE tax.taxonomy = %s AND r.object_id = parent.ID AND t.slug NOT IN ($placeholders)) = 0", $values );
                $sql .= ' AND ';

                // Need the count added to the values array.
                array_push( $values, count($values) - 1 );
                $sql .= $wpdb->prepare("(SELECT COUNT(*) FROM {$wpdb->term_taxonomy} AS tax JOIN {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id) JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id) WHERE tax.taxonomy = %s AND r.object_id = parent.ID AND t.slug IN ($placeholders)) = %d", $values );
                $sql .= ')';

                return $sql;
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is empty' ) ? 'NOT' : '';
                return $wpdb->prepare("$negator EXISTS (SELECT 1 FROM {$wpdb->term_taxonomy} AS tax JOIN {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id) JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id) WHERE tax.taxonomy = %s AND r.object_id = parent.ID)", $taxonomy);
            break;
        }
    }

    public function maybe_add_catalog_visibility_joins( $sql_joins ) {
        global $wpdb;

        if ( strpos( $sql_joins, 'visibility_exclude_from_catalog' ) === false ) {
            $sql_joins .= "
                LEFT JOIN (
                    SELECT
                        r.object_id AS product_id
                    FROM
                        {$wpdb->term_taxonomy} AS tax
                    JOIN
                        {$wpdb->terms} AS t ON (t.term_id = tax.term_id AND t.name = 'exclude-from-catalog')
                    JOIN
                        {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id)
                    WHERE
                        tax.taxonomy = 'product_visibility'
                ) AS visibility_exclude_from_catalog ON (visibility_exclude_from_catalog.product_id = post.ID)
                LEFT JOIN (
                    SELECT
                        r.object_id AS product_id
                    FROM
                        {$wpdb->term_taxonomy} AS tax
                    JOIN
                        {$wpdb->terms} AS t ON (t.term_id = tax.term_id AND t.name = 'exclude-from-search')
                    JOIN
                        {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id)
                    WHERE
                        tax.taxonomy = 'product_visibility'
                ) AS visibility_exclude_from_search ON (visibility_exclude_from_search.product_id = post.ID)
            ";
        }

        return $sql_joins;
    }

    public function catalog_visibility_search( $filter_type, $values, &$sql_joins ) {
        global $wpdb;

        $sql_joins = $this->maybe_add_catalog_visibility_joins( $sql_joins );

        if ( !is_array( $values ) ) {
            $values = array();
        }

        $sql = "( post.post_type != 'product_variation' AND ( ";

        $negator = ( $filter_type == 'is none of' ) ? 'NOT' : '';

        foreach ( $values as $value ) {
            switch ( $value ) {
                case 'visible':
                    $sql .= " ( $negator (visibility_exclude_from_catalog.product_id IS NULL AND visibility_exclude_from_search.product_id IS NULL) ) ";
                break;

                case 'hidden':
                    $sql .= " ( $negator (visibility_exclude_from_catalog.product_id IS NOT NULL AND visibility_exclude_from_search.product_id IS NOT NULL) ) ";
                break;

                case 'search':
                    $sql .= " ( $negator (visibility_exclude_from_catalog.product_id IS NOT NULL AND visibility_exclude_from_search.product_id IS NULL) ) ";
                break;

                case 'catalog':
                    $sql .= " ( $negator (visibility_exclude_from_catalog.product_id IS NULL AND visibility_exclude_from_search.product_id IS NOT NULL) ) ";
                break;

                default:
                    $sql .= ' 1 = 1 ';
                break;
            }

            if ( $filter_type == 'is none of' ) {
                $sql .= ' AND ';
            } else {
                $sql .= ' OR  ';
            }
        }

        $sql = substr( $sql, 0, -5 );

        return $sql . ' ) )';
    }

    public function product_type_search( $filter_type, $values ) {
        global $wpdb;

        $sql = "( post.post_type != 'product_variation' AND ";

        $sql .= $this->taxonomy_search( 'product_type', $filter_type, $values );

        return $sql . ' )';
    }

    public function boolean_search( $field_name, $filter_type, $value ) {
        global $wpdb;

        switch( $filter_type ) {
            case 'is checked':
                return "LOWER(TRIM($field_name)) IN ('yes', 'true', '1')";
            break;

            default:
                return "COALESCE(NULLIF(LOWER(TRIM($field_name)), ''), 'no') IN ('no', 'false', '0')";
            break;
        }
    }

    public function attributes_search( $field_name, $filter_type, $values ) {
        global $wpdb;

        if ( is_array( $values ) && count( $values ) > 0 ) {
            foreach ( $values as $key => $value ) {
                $values[ $key ] = stripslashes( $value );
            }
        } else {
            $values = array();
        }

        $slugs = implode( ', ', array_fill( 0, count( $values ), '%s' ) );

        switch( $filter_type ) {
            case 'is any of':
            case 'is none of':
                $negator = ( $filter_type == 'is none of' ) ? 'NOT' : '';

                $wpdb_values = $values;
                $wpdb_values[] = $field_name;

                $simple_attribute = $wpdb->prepare("
                    post.post_type != 'product_variation' AND (
                        $negator EXISTS (
                            SELECT 1
                            FROM {$wpdb->term_relationships} AS r
                            JOIN {$wpdb->term_taxonomy} AS tax ON (tax.term_taxonomy_id = r.term_taxonomy_id)
                            JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id)
                            WHERE r.object_id = post.ID AND t.slug IN ($slugs) AND tax.taxonomy = %s
                        )
                    )
                ",
                $wpdb_values );

                $variable_attribute = $this->variation_attributes_search( $field_name, $filter_type, $values );

                return "($simple_attribute OR $variable_attribute)";
            break;

            case 'is all of':

                $wpdb_values = $values;
                $wpdb_values[] = $field_name;
                $wpdb_values[] = count( $values );

                $simple_attribute = $wpdb->prepare("
                    post.post_type != 'product_variation' AND (
                        SELECT COUNT(*)
                        FROM {$wpdb->term_relationships} AS r
                        JOIN {$wpdb->term_taxonomy} AS tax ON (tax.term_taxonomy_id = r.term_taxonomy_id)
                        JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id)
                        WHERE r.object_id = post.ID AND t.slug IN ($slugs) AND tax.taxonomy = %s
                    ) = %d
                ",
                $wpdb_values );

                $variable_attribute = $this->variation_attributes_search( $field_name, $filter_type, $values );

                return "($simple_attribute OR $variable_attribute)";
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is empty' ) ? 'NOT' : '';
                $simple_attribute = $wpdb->prepare("
                    post.post_type != 'product_variation' AND $negator EXISTS (
                        SELECT 1
                        FROM {$wpdb->term_relationships} AS r
                        JOIN {$wpdb->term_taxonomy} AS tax ON (tax.term_taxonomy_id = r.term_taxonomy_id)
                        JOIN {$wpdb->terms} AS t ON (t.term_id = tax.term_id)
                        WHERE r.object_id = post.ID
                        AND tax.taxonomy = %s
                    )
                ",
                $field_name);

                // $variable_attribute = $this->variation_attributes_search( $field_name, $filter_type, $values );
                // return "($simple_attribute OR $variable_attribute)";
                return $simple_attribute;
            break;

        }
    }

    public function variation_attributes_search( $field_name, $filter_type, $values ) {
        global $wpdb;

        $slugs = implode( ', ', array_fill( 0, count( $values ), '%s' ) );

        switch( $filter_type ) {
            case 'is any of':
            case 'is none of':
                $negator = ( $filter_type == 'is none of' ) ? 'NOT' : '';
                array_unshift( $values, 'attribute_' . $field_name );
                return $wpdb->prepare("
                    post.post_type = 'product_variation' AND (
                        $negator EXISTS (
                            SELECT 1
                            FROM {$wpdb->postmeta} AS m
                            WHERE
                                m.post_id = post.ID
                                AND m.meta_key = %s
                                AND m.meta_value IN ($slugs)
                        )
                    )
                ",
                $values);
            break;

            case 'is all of':
                array_push( $values, count($values) );
                array_unshift( $values, 'attribute_' . $field_name );
                return $wpdb->prepare("
                    post.post_type = 'product_variation' AND (
                        SELECT COUNT(*)
                        FROM {$wpdb->postmeta} AS m
                        WHERE
                            m.post_id = post.ID
                            AND m.meta_key = %s
                            AND m.meta_value IN ($slugs)
                    ) = %d
                ",
                $values);
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is empty' ) ? 'NOT' : '';
                return $wpdb->prepare("
                    post.post_type = 'product_variation' AND $negator EXISTS (
                        SELECT 1
                        FROM {$wpdb->postmeta} AS m
                        WHERE
                            m.post_id = post.ID
                            AND m.meta_key = %s
                    )
                ",
                $field_name);
            break;

        }
    }

    public function custom_attributes_search( $field_name, $filter_type, $values ) {
        global $wpdb;

        if ( is_array( $values ) && count( $values ) > 0 ) {
            foreach ( $values as $key => $value ) {
                $values[ $key ] = stripslashes( $value );
            }
        }

        $custom_attributes = PWBE_Attributes::get_custom_attributes();
        $post_ids = array();

        switch( $filter_type ) {
            case 'is any of':
            case 'is none of':
                foreach ( $values as $value_slug ) {
                    if ( isset( $custom_attributes[ $field_name ][ $value_slug ] ) ) {
                        foreach ( $custom_attributes[ $field_name ][ $value_slug ] as $post_id ) {
                            $post_ids[] = $post_id;
                        }
                    }
                }
                $post_ids = array_unique( $post_ids );

                $placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );

                $variable_attribute = $this->variation_attributes_search( $field_name, $filter_type, $values );

                if ( $filter_type == 'is any of' ) {
                    $parent_attribute = $wpdb->prepare("post.ID IN ($placeholders)", $post_ids);
                    return "($parent_attribute OR $variable_attribute)";
                } else {
                    $parent_attribute = $wpdb->prepare("post.ID NOT IN ($placeholders)", $post_ids);
                    return "($parent_attribute AND $variable_attribute)";
                }
            break;

            case 'is all of':

                // Extract a list of product IDs and their slugs for comparison later
                $product_ids = array();
                foreach ( $custom_attributes[ $field_name ] as $slug => $ids ) {
                    foreach ( $ids as $product_id ) {
                        $product_ids[ $product_id ][] = $slug;
                    }
                }

                // Sort so that we can compare the arrays directly.
                sort( $values );

                // Now populate $post_ids with the products that have exactly the right number of values ("is all of").
                $post_ids = array();
                foreach ( $product_ids as $product_id => $slugs ) {
                    sort( $slugs );

                    if ( $slugs == $values ) {
                        $post_ids[] = $product_id;
                    }
                }

                $post_ids = array_unique( $post_ids );
                if ( count( $post_ids ) > 0 ) {
                    $placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
                    return $wpdb->prepare("post.ID IN ($placeholders)", $post_ids);
                } else {
                    return '1 = 0';
                }
            break;

            case 'is empty':
            case 'is not empty':
                if ( isset( $custom_attributes[ $field_name ] ) ) {
                    foreach ( $custom_attributes[ $field_name ] as $products ) {
                        foreach ( $products as $post_id ) {
                            $post_ids[] = $post_id;
                        }
                    }
                    $post_ids = array_unique( $post_ids );
                }

                $variable_attribute = $this->variation_attributes_search( $field_name, $filter_type, $values );

                $negator = ( $filter_type == 'is empty' ) ? 'NOT' : '';
                $placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
                $parent_attribute = $wpdb->prepare("post.ID $negator IN ($placeholders)", $post_ids);

                return "($parent_attribute AND $variable_attribute)";
            break;
        }
    }

    public function multiselect_search( $field_name, $filter_type, $values ) {
        global $wpdb;

        switch( $filter_type ) {
            case 'is any of':
            case 'is none of':
                if ( is_array( $values ) && count( $values ) > 0 ) {
                    $values = array_map( 'trim', $values );
                    $placeholders = implode( ', ', array_fill( 0, count( $values ), '%s' ) );
                }

                $negator = ( $filter_type == 'is none of' ) ? 'NOT' : '';
                return $wpdb->prepare("$field_name $negator IN ($placeholders)", $values);
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is not empty' ) ? 'NOT' : '';
                return "NULLIF(TRIM($field_name), '') IS $negator NULL";
            break;
        }
    }

    public function image_search( $field_name, $filter_type, $values ) {
        global $wpdb;

        $field = "NULLIF(TRIM($field_name), '')";

        $placeholder_image = get_option( 'woocommerce_placeholder_image', 0 );
        if ( !empty( $placeholder_image ) ) {
            $field = "NULLIF($field, '$placeholder_image')";
        }

        $negator = ( $filter_type == 'is set' ) ? 'NOT' : '';
        return "$field IS $negator NULL";
    }

    public function build_order_by( $post ) {
        $order_by = 'post_title';
        $direction = 'ASC';

        if ( !empty( $post['order_by_desc'] ) ) {
            $direction = 'DESC';
        }

        if ( !empty( $post['order_by'] ) ) {
            $column = PWBE_Columns::get_by_field( $post['order_by'] );
            if ( !empty( $column ) ) {
                if ( $column['type'] == 'currency' ) {
                    $order_by = "LENGTH(`$column[field]`) $direction, `$column[field]` $direction";
                } else {
                    if ( $column['field'] == '_featured' && PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
                        $order_by = "is_featured_product $direction";
                    } else if ( $column['field'] == '_visibility' ) {
                        $order_by = "
                            CASE
                                WHEN (visibility_exclude_from_catalog_product_id IS NULL AND visibility_exclude_from_search_product_id IS NULL) THEN 1
                                WHEN (visibility_exclude_from_catalog_product_id IS NOT NULL AND visibility_exclude_from_search_product_id IS NOT NULL) THEN 2
                                WHEN (visibility_exclude_from_catalog_product_id IS NOT NULL AND visibility_exclude_from_search_product_id IS NULL) THEN 3
                                ELSE 4
                            END $direction
                        ";
                    } else if ( $column['type'] == 'number' ) {
                        $order_by = "CAST( `$column[field]` AS DECIMAL(10,6) ) $direction";
                    } else {
                        $order_by = "`$column[field]` $direction";
                    }
                }
            }
        }

        $order_by .= ", COALESCE(NULLIF(parent_post_id, 0), post_id), CASE WHEN product_type = 'variation' THEN menu_order ELSE -1000 END, post_id";

        return $order_by;
    }

    public function variable_product_types() {
        $variable_product_types = array(
            'variable',
            'variable-subscription'
        );

        // Allow other developers to add to this list.
        $variable_product_types = apply_filters( 'pwbe_variable_product_types', $variable_product_types, $this );

        // Wrap them in single quotes for the queries.
        array_walk( $variable_product_types, function( &$x ) { $x = "'$x'"; } );

        return implode( ',', $variable_product_types );
    }

    // Apply the query to find Featured flag, if it hasn't been added already.
    private function maybe_add_featured_joins( $query ) {
        global $wpdb;

        if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
            $featured_query = "
                LEFT JOIN (
                    SELECT
                        r.object_id AS product_id
                    FROM
                        {$wpdb->term_taxonomy} AS tax
                    JOIN
                        {$wpdb->terms} AS t ON (t.term_id = tax.term_id AND t.name = 'featured')
                    JOIN
                        {$wpdb->term_relationships} AS r ON (r.term_taxonomy_id = tax.term_taxonomy_id)
                    WHERE
                        tax.taxonomy = 'product_visibility'
                ) AS featured_products ON (featured_products.product_id = parent.ID)
            ";
        } else {
            $featured_query = "
                LEFT JOIN
                    {$wpdb->postmeta} AS meta__featured ON (meta__featured.post_id = parent.ID AND meta__featured.meta_key = '_featured')
            ";
        }

        return str_replace( '-- featured_placeholder', $featured_query, $query );
    }
}

global $pwbe_sql_builder;
$pwbe_sql_builder = new PWBE_SQL_Builder();

endif;

?>