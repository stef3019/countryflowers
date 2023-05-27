<?php

add_action('spring_plant_after_main_content','woocommerce_output_related_products', 20);
add_action('spring_plant_after_main_content','woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);
remove_action('woocommerce_after_single_product_summary','woocommerce_upsell_display',15);
