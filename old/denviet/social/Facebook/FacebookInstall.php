<?php
namespace Kalephan\Social\Facebook;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class FacebookInstall {

    const VERSION = 0.01;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        //lks_config_set('vendor version lks.social-facebook', self::VERSION);
    }

    private static function up_0_01() {
        /*DB::table('route')->insert(array(
            array(
                'title' => 'Facebook Connect',
                'cache' => null,
                'path' => 'social-fb',
                'class' => '\Kalephan\SocialFb\SocialFb@connect',
                'arguments' => null,
                'access' => 'User: login',
                'active' => 1,
            ),
        ));*/

        //lks_config_set('vendor version lks.social-facebook', 0.01);
    }
}
