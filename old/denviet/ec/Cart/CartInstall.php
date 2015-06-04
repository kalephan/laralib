<?php
namespace Kalephan\Ec\Cart;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CartInstall {

    const VERSION = 0.01;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        //lks_config_set('vendor version lks.ec-cart', self::VERSION);
    }

    private static function up_0_01() {
        DB::table('access')->insert(array(
            array(
                'name' => 'Ec Cart: Cart review',
                'class' => null,
                'role' => '1|2',
            ),
        ));

        lks_config_set('vendor version lks.ec-cart', 0.01);
    }
}
