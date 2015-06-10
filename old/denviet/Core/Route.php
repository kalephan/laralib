<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\Config;

class Route
{

    function getRouteItem($paths, &$path = '')
    {
        if (is_string($paths)) {
            $paths = explode('/', $paths);
        }
        $ancestors = lks_menu_ancestors($paths);
        
        foreach ($ancestors as $value) {
            if ($menu = Config::get('route_' . Config::get('lks.site', 'frontend') . ".$value")) {
                $path = $value;
                return $menu;
            }
        }
        
        return false;
    }
}