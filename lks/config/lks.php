<?php
return [
    // Chovip only
    
    // LKS Customized
    'theme_default' => realpath(base_path('themes/f15')),
    'theme_backend' => realpath(base_path('themes/b15')),
    'sitename' => 'ChoVIP.vn',
    'asset_compress_css'=> false,
    'asset_compress_js'=> false,
    
    // LKS Default
    'theme_engine'=> realpath(base_path('vendor/kalephan/lks/views')),
    'cache_ttl' => 10, // 10 minutes
    'items_per_page'=> 20, // items per a page pagination
    'link_secure' => false, // make https:// link
];