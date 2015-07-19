<?php
return [
    // LKS Customized
    'theme_default' => realpath(base_path('vendor/kalephan/lks/views')),
    'sitename' => 'Laravel Kick-Start',

    // LKS Default
    'theme_engine'=> realpath(base_path('vendor/kalephan/lks/views')),
    'cache_lifetime' => 10, // 10 minutes
    'items_per_page'=> 20, // items per a page pagination
    'link_secure' => false, // make https:// link
    'form_lifetime' => 120, // 120 minutes
];