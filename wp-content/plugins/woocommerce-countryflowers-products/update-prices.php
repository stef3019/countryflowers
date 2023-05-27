<?php


 function update_from_csv () {

    //get CSV with updated prices: cols: SKU, Regular Price (in decimal format)
    
     
    $csv = readCSV('price_updates/artificial_plants_test_one.csv'); // Make sure this exsists or you will have a forever error log.
    //foreach row, update regular price where SKU = col1
    echo 'CSV:';
    print_r($csv);

 } 
 add_shortcode( 'sc_playground', 'update_from_csv' );


 function readCSV($csvFile){ 
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle) ) {
        $line_of_text[] = fgetcsv($file_handle, 0);
    }
    fclose($file_handle);
    return $line_of_text;
}