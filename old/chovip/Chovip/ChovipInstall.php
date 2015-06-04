<?php
namespace Chovip\Chovip;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChovipInstall {

    const VERSION = 0.0107;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        lks_config_set('vendor version chovip.chovip', self::VERSION);
    }

    private static function up_0_01() {
        DB::table('events')->insert(array(
            array(
                'name' => 'lks.controllerBefore',
                'class' => '\Chovip\Chovip\ChovipEvent@JSSetting',
            ),
        ));

        DB::table('menus')->insert(array(
            array(
                'code' => 'chovip',
                'title' => 'Chovip',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => 999,
            ),
        ));

        lks_config_set('lks queue use', 1);
        lks_config_set('sitename', 'ChoVIP.vn');
        lks_config_set('rfm tinymce config filemanager_crossdomain', 'true');
        lks_config_set('rfm tinymce config external_filemanager_path', Config::get('lks.site_urls_cdn', '') . '/rfm/filemanager/');
        lks_config_set('rfm config dialog path', Config::get('lks.site_urls_cdn', '') . '/rfm/filemanager/dialog.php');
        lks_config_set('rfm config crossdomain', 1);
        lks_config_set('cdn image style make', 1);
        lks_config_set('urlalias default prefix', 'v');

        $external_plugin = config('lks.tinymce config external_plugins', [], true);
        $external_plugin['filemanager'] = '"filemanager":"' . Config::get('lks.site_urls_cdn', '') . '/rfm/filemanager/plugin.min.js"';
        lks_config_set('tinymce config external_plugins', $external_plugin);

        $views = config('lks.views dir collection', [], true);
        $views[] = base_path() . '/vendor/inetco/chovip/views';
        lks_config_set('views dir collection', $views);

        lks_config_set('vendor version chovip.chovip', 0.01);
    }
}
