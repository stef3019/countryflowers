<?php
/**
 * The template for displaying header
 */
$header_enable = Spring_Plant()->options()->get_header_enable();
if ($header_enable !== 'on') return;
Spring_Plant()->helper()->getTemplate('header/desktop');
Spring_Plant()->helper()->getTemplate('header/mobile');


