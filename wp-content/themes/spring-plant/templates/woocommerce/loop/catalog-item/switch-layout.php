<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 07/08/2017
 * Time: 8:11 SA
 */
$catalog_layout = Spring_Plant()->options()->get_product_catalog_layout();
?>
<ul class="gf-shop-switch-layout gf-inline">
    <li class="<?php echo esc_attr($catalog_layout === 'grid' ? 'active' : ''); ?>"><a data-toggle="tooltip" href="#" data-layout="grid" title="<?php esc_attr_e('Grid','spring-plant'); ?>"><i class="flaticon-menu"></i></a></li>
    <li class="<?php echo esc_attr($catalog_layout === 'list' ? 'active' : ''); ?>"><a data-toggle="tooltip" href="#" data-layout="list" title="<?php esc_attr_e('List','spring-plant'); ?>"><i class="flaticon-list-menu"></i></a></li>
</ul>