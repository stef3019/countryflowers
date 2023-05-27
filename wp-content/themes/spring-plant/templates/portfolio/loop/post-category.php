<?php
echo get_the_term_list(get_the_ID(),Spring_Plant()->portfolio()->get_taxonomy_category(),'<h6 class="portfolio-cat">',', ','</h6>');