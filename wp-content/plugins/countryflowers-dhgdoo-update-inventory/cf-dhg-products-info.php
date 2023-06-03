<?php 
global $wpdb;
$fileurl = cf_get_today_json_filepath();
$jsonData = file_get_contents($fileurl);

// Decode the JSON data into an associative array
$data = json_decode($jsonData, true);
$data = $data['products'];
// Count the number of items in the array
$itemCount = count($data);

$simple = 0;
$variant = 0;
$c = 0;
$s = 0;
// Output the item count
echo "Total Number of Products: " . $itemCount;
foreach ($data as $item) {
    if (isset($item['categories'][0]['id']) && (!in_array($item['categories'][0]['name'], $cNames))) {
        $cNames[] = $item['categories'][0]['name'];
        $categories[$c]['name'] = $item['categories'][0]['name'];
        $categories[$c]['id'] = $item['categories'][0]['id'];
        $c++;
    }

    if (isset($item['subCategories'][0]['id']) && (!in_array($item['subCategories'][0]['name'], $subNames))) {
        $subNames[] = $item['subCategories'][0]['name'];
        $subCategories[$s]['name'] = $item['subCategories'][0]['name'];
        $subCategories[$s]['id'] = $item['subCategories'][0]['id'];
        $subCategories[$s]['parent'] = $item['categories'][0]['name'];
        $subCategories[$s]['parent_id'] = $item['categories'][0]['id'];
        $s++;
    }

    if ($item['code'] != $item['variant']) {
        $variantItems[] = $item['variant'];
    } else {
        $simpleItems[] = $item['code'];
    }   
}


echo "</br>Total Number of Categories: " . count($cNames);
echo "</br>Total Number of Subcategories: " . count($subNames);
echo "</br>"; 
echo "</br>Simple Items: " . count($simpleItems);
echo "</br>Variants: " . count($variantItems);
usort($subCategories, "sortByParentId");
echo "<ul>";  
$cat = $subCategories[0]['parent_id'];
echo '<strong>'.$subCategories[0]['parent'].'</strong>';


foreach ($subCategories as $key => $subCat) {
    $id = $subCat['id'];

    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM `wp_category_lookup` WHERE `dhg_id` = '$id' ");

    if ($subCat['parent_id'] != $cat) {
        echo '</br><strong>'.$subCat['parent'].'</strong>';
    }
    if ($rowcount == 0) {
        echo '<li><strong> - '.$subCat['name'].' (id: '.$subCat['id'].')</strong></li>';
    } else {
        echo '<li> - '.$subCat['name'].' (id: '.$subCat['id'].')</li>';
    }
        
    $cat = $subCat['parent_id'];
}
echo "</ul>";


// Custom comparison function to sort by "parent" key
function sortByParentId($a, $b) {
    return $a["parent_id"] - $b["parent_id"];
}

?>