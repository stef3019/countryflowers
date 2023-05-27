<?php
/**
 * The template for displaying cat-filter.php
 *
 * @var $settingId
 * @var $pagenum_link
 * @var $filter_vertical
 * @var $filter_type
 * @var $post_type
 * @var $category_filter
 * @var $current_cat
 * @var $taxonomy
 */
$parentcats = get_ancestors($current_cat, 'product_cat');
$parent = $parentcats[0];


if ($current_cat == -1) {
   // echo 'all main';
    $args = array(
        'hide_empty'    => 1,
        'orderby' => 'include',
        'taxonomy' => $taxonomy,
        'parent' => 0
    );
} elseif ($parent > 0) {
   // echo 'has a parent';
    $args = array(
        'hide_empty'    => 1,
        'orderby' => 'include',
        'taxonomy' => $taxonomy,
        'parent' => $parent
    );
} else {
   // echo 'is parent';
    $args = array(
        'hide_empty'    => 1,
        'orderby' => 'include',
        'taxonomy' => $taxonomy,
        'child_of' => $current_cat
    );
    $parent =  $current_cat;
}

//if is a subcategory, show peers (parent != 0)
// if is a main category, show children ()

// echo $parentcats;

// print_r($parentcats);

if (is_array($category_filter)){
    $args['include'] = $category_filter;
}


$prettyTabsOptions = array(
    'more_text' => '<span>+MORE+</span>'
);


$cate_attributes = array();
if($filter_vertical == true) {
    $cate_attributes[] = 'data-filter-vertical=1';
} else {
    $cate_attributes[] = 'data-filter-vertical=0';
}

if(!empty($filter_type)) {
    $cate_attributes[] = ' data-filter-type=' . $filter_type;
}
$categories = get_categories( $args );
?>
<ul class="nav nav-tabs gf-cate-filter" >
    <?php
    $cate_link = get_post_type_archive_link($post_type);
    $main_cate_link = get_term_link( $parent, 'product_cat' );
    ?>

    <li class="<?php echo esc_attr($parent == $current_cat ? ' active' : '') ?>">
        <a title="<?php esc_attr_e('All', 'spring-plant') ?>" href="<?php echo esc_url($main_cate_link); ?>"><?php esc_html_e('All', 'spring-plant') ?></a>
    </li>
  
    
    <?php 
       // var_dump($categories);
        foreach ($categories as $category):
            $cate_link = trailingslashit(get_term_link($category));
            ?>
            <li class="<?php echo esc_attr($category->cat_ID == $current_cat ? ' active' : '') ?><?php echo esc_attr($category->name) ?>">
                <a title="<?php echo esc_attr($category->name) ?>" href="<?php echo esc_url($cate_link) ?>"><?php echo esc_html($category->name)?></a>
            </li>
    <?php endforeach; ?>
</ul>