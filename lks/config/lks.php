<?php
return [
    // LKS Customized
    'theme_default' => realpath(base_path('themes/f15')),
    'sitename' => 'Laravel Kick-Start',

    // LKS Default
    'theme_engine'=> realpath(base_path('vendor/kalephan/lks/views')),
    'cache_ttl' => 10, // 10 minutes
    'items_per_page'=> 20, // items per a page pagination
    'link_secure' => false, // make https:// link
];