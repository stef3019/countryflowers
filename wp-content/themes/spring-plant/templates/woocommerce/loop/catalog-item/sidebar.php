<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 07/08/2017
 * Time: 8:11 SA
 */

$woocommerce_customize_sidebar = Spring_Plant()->options()->get_woocommerce_customize_sidebar();
if (is_active_sidebar($woocommerce_customize_sidebar)){
    dynamic_sidebar($woocommerce_customize_sidebar);
}