<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04/07/2017
 * Time: 10:00 SA
 */
$reading_process_enable = Spring_Plant()->options()->get_single_reading_process_enable();
if (!is_singular('post') || $reading_process_enable !== 'on') return;
?>
<div id="gsf-reading-process"></div>
