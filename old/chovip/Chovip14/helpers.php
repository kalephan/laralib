<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\HTML;

if (! function_exists('fe15_adv_load')) {

    function fe15_adv_load($position)
    {
        switch ($position) {
            case 1:
                return '<div class="banner_right no-device-768"><img src="/fe15//f14/images/adv_right1.png"><br><img src="/fe15//f14/images/adv_right2.png" style="margin-top:15px;"></div>';
            
            case 3:
                if (! lks_instance_get()->response->isFrontPage() || lks_instance_get()->response->isUserpanel()) {
                    return '';
                }
                
                return '<div class="no-device-768 banner_big"><img src="/fe15//f14/images/banner_index_990_90.png" class="mar_top7"></div>';
            
            case 4:
                return '<div class="no-device-768 banner"><img src="/fe15//f14/images/banner_index01_490_100.png"><img src="/fe15//f14/images/banner_index02_490_100.png"></div>';
            
            case 5:
                return '<div class="no-device-768 banner"><img src="/fe15//f14/images/banner_index03_490_100.png"><img src="/fe15//f14/images/banner_index04_490_100.png"> </div>';
            
            case 7:
                return '<div class="no-device-480 banner_big"><img src="/fe15//f14/images/banner_footer.png" width="990" height="70" class="mar_bottom7"></div>';
            
            default:
                return '';
        }
    }
}

if (! function_exists('fe15_fancybox_modal')) {

    function fe15_fancybox_modal($url, $title, $attributes = [], $target = "#myModal")
    {
        $attributes['class'] .= 'fancybox-modal';
        
        return '<a ' . HTML::attributes($attributes) . ' data-fancybox-type="iframe" href="' . lks_url($url) . '">' . $title . '</a>';
    }
}
