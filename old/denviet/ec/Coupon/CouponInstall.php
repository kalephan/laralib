<?php
namespace Kalephan\Ec\Coupon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CouponInstall
{

    const VERSION = 0.01;

    public static function up($prev)
    {
        if ($prev < 0.01 && $prev < self::VERSION) {
            self::up_0_01();
        }
        if ($prev < self::VERSION) {
            self::up_0_01x();
        }
    }
    
    // Target: 01-01-2015
    private static function up_0_01x()
    {
        // lks_config_set('vendor version lks.ec-coupon', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::table('ec_products', function ($table) {
            $table->integer('coupon_value')->nullable();
            $table->char('coupon_type', 16)->nullable();
            $table->dateTime('coupon_start')->nullable();
            $table->dateTime('coupon_end')->nullable();
        });
        
        DB::table('events')->insert(array(
            array(
                'name' => 'entity.structureAlter.ec_products',
                'class' => '\Kalephan\Ec\Coupon\CouponEvent@structureAlterProduct'
            )
        ));
        
        lks_config_set('vendor version lks.ec-coupon', 0.01);
    }
}
