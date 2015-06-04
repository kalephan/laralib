<?php
namespace Kalephan\Ec\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductInstall {

    const VERSION = 0.01;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        //lks_config_set('vendor version lks.ec-product', self::VERSION);
    }

    private static function up_0_01() {
        Schema::create('ec_products', function($table) {
            $table->bigIncrements('id');
            $table->string('title', 256);
            $table->text('short_desc')->nullable();
            $table->integer('price')->nullable();
            $table->string('image', 256)->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->boolean('active')->default(0);

            $table->index('price');
        });

        $saleman = DB::table('roles')->where('title', 'Salesman')->pluck('id');
        DB::table('access')->insert(array(
            array(
                'name' => 'Ec Product: update',
                'class' => '',
                'role' => 3,
            ),
            array(
                'name' => 'Ec Product: own update',
                'class' => '\Kalephan\Ec\Product\ProductEntity@isOwn',
                'role' => $saleman,
            ),
        ));

        lks_config_set('vendor version lks.ec-product', 0.01);
    }
}
