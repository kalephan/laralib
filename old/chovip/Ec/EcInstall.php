<?php
namespace Chovip\Ec;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EcInstall
{

    const VERSION = 0.0104;

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
        Schema::table('ec_products', function ($table) {
            $table->integer('topic')
                ->unsigned()
                ->nullable();
        });
        
        lks_config_set('vendor version chovip.ec', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::table('ec_shops', function ($table) {
            $table->string('shop_address', 256)->nullable();
            $table->integer('shop_province_id')
                ->unsigned()
                ->nullable();
            $table->integer('shop_district_id')
                ->unsigned()
                ->nullable();
            $table->string('shop_homephone', 32)->nullable();
            $table->string('shop_mobile', 32)->nullable();
            $table->string('shop_website', 128)->nullable();
            $table->text('shop_chat_nick')->nullable();
            $table->string('shop_image', 256)->nullable();
            $table->string('approve', 128)->nullable();
        });
        
        Schema::create('ec_shops_extend', function ($table) {
            $table->increments('id');
            $table->text('shop_paymenth')->nullable();
            $table->text('shop_shipmenth')->nullable();
            $table->text('shop_aboutus')->nullable();
            $table->text('shop_contact')->nullable();
            $table->string('approve', 128)->nullable();
        });
        
        Schema::table('ec_products', function ($table) {
            $table->tinyInteger('label')->nullable();
        });
        
        Schema::table('ec_topics', function ($table) {
            $table->integer('category_id')
                ->unsigned()
                ->nullable();
            $table->tinyInteger('shipping')->nullable();
            $table->boolean('is_promotion')->default(0);
            $table->integer('coupon_value')->nullable();
            $table->char('coupon_type', 16)->nullable();
            $table->dateTime('coupon_start')->nullable();
            $table->dateTime('coupon_end')->nullable();
            $table->integer('province_id')
                ->unsigned()
                ->nullable();
        });
        
        $saleman = DB::table('roles')->where('title', 'Salesman')->pluck('id');
        DB::table('access')->insert(array(
            array(
                'name' => 'Ec Shop: confirmation',
                'class' => '\Chovip\Ec\ShopEvent@allowConfirmation',
                'role' => '2'
            ),
            array(
                'name' => 'Ec Shop: finalize',
                'class' => '\Chovip\Ec\ShopEvent@allowFinalize',
                'role' => '2'
            ),
            array(
                'name' => 'Ec Shop: administrator',
                'class' => null,
                'role' => 3
            )
        ));
        
        DB::table('events')->insert(array(
            array(
                'name' => 'entity.structureAlter.ec_shops',
                'class' => '\Chovip\Ec\ShopEvent@alterEntityStructureEcShop',
                'weight' => 0
            ),
            array(
                'name' => 'form.formAlter.lks-ec-shop-shopentity-showcreateform',
                'class' => '\Chovip\Ec\ShopEvent@alterCreateForm',
                'weight' => 0
            ),
            array(
                'name' => 'form.formAlter.lks-ec-shop-shopentity-showupdateform',
                'class' => '\Chovip\Ec\ShopEvent@alterUpdateForm',
                'weight' => 0
            ),
            array(
                'name' => 'form.formValueAlter.lks-ec-shop-shopentity-showcreateform',
                'class' => '\Chovip\Ec\ShopEvent@alterCreateFormValue',
                'weight' => 0
            ),
            array(
                'name' => 'form.formValueAlter.lks-ec-shop-shopentity-showupdateform',
                'class' => '\Chovip\Ec\ShopEvent@alterUpdateFormValue',
                'weight' => 0
            ),
            array(
                'name' => 'entity.structureAlter.ec_topics',
                'class' => '\Chovip\Ec\TopicEvent@alterEntityStructureEcTopic',
                'weight' => 0
            ),
            array(
                'name' => 'form.formAlter.lks-ec-topic-topicentity-showcreateform',
                'class' => '\Chovip\Ec\TopicEvent@alterCreateForm',
                'weight' => 0
            ),
            array(
                'name' => 'form.formValueAlter.lks-ec-topic-topicentity-showcreateform',
                'class' => '\Chovip\Ec\TopicEvent@alterCreateFormValue',
                'weight' => 0
            ),
            array(
                'name' => 'entity.structureAlter.ec_topics',
                'class' => '\Kalephan\Ec\Coupon\CouponEvent@structureAlterProduct',
                'weight' => 0
            )
        ));
        
        DB::table('styles')->insert([
            'style' => 'shop_avatar',
            'width' => 90,
            'height' => 90,
            'type' => 'scale-and-crop',
            'is_upsize' => 1
        ]);
        
        lks_config_set('vendor version chovip.ec', 0.01);
    }
}
