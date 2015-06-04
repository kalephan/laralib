<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\Config;

class CoreEvent {
    public function makeURLAlterUACP(&$path, &$http) {
        if ($path != '/'
            && substr($path, 0, 2) != '//'
            && substr($path, 0, 7) != 'http://'
            && substr($path, 0, 8) != 'https://'
        ) {
            $path = trim($path, '/');

            $matches = [];
            $pattern = '/{([A-z0-9-_.]+)?}/';
            if (preg_match($pattern, $path, $matches)) {
                $http = Config::get('lks.site_urls_' . $matches[1], '');
                $path = trim(substr($path, strlen($matches[1]) + 2), '/');
            }
        }
    }

    public function uriAlterUACP(&$uri) {
        if ($uri != '/') {
            $uri = trim($uri, '/');
            $matches = [];
            $pattern = '/{([A-z0-9-_.]+)?}/';
            if (preg_match($pattern, $uri, $matches)) {
                $uri = trim(substr($uri, strlen($matches[1]) + 2), '/');
            }
        }
    }
}