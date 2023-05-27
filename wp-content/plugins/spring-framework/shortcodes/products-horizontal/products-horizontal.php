<?php
/**
 * The template for displaying products-horizontal.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class WPBakeryShortCode_GSF_Products_Horizontal extends G5P_ShortCode_Base {
    function order_by_comment_date_post_clauses($args){
        global $wpdb;
        $args['join'] .= "
                LEFT JOIN (
                    SELECT comment_post_ID, MAX(comment_date)  as  comment_date
                    FROM $wpdb->comments
                    WHERE comment_approved = 1
                    GROUP BY comment_post_ID
                ) as wp_comments ON($wpdb->posts.ID = wp_comments.comment_post_ID)
            ";
        $args['orderby'] = "wp_comments.comment_date DESC";
        return $args;
    }
}