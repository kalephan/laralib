<?php
namespace Kalephan\Ec\Shop;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopInstall
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
        // lks_config_set('vendor version lks.ec-shop', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::create('ec_shops', function ($table) {
            $table->increments('id');
            $table->string('title', 256);
            $table->string('path', 128)->unique();
            $table->boolean('active')->default(0);
            $table->integer('created_by')
                ->unsigned()
                ->nullable();
            $table->integer('updated_by')
                ->unsigned()
                ->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            
            $table->index('path');
        });
        
        Schema::create('ec_shops_pathpending', function ($table) {
            $table->string('path', 128)->unique();
            
            $table->primary('path');
        });
        
        if (! DB::table('roles')->where('title', 'Salesman')->first()) {
            DB::table('roles')->insert(array(
                array(
                    'title' => 'Salesman'
                )
            ));
        }
        $saleman = DB::table('roles')->where('title', 'Salesman')->pluck('id');
        lks_config_set('ec shop role salesman', [
            $saleman
        ]);
        
        DB::table('menus')->insert(array(
            array(
                'code' => 'ec-shop',
                'title' => 'Shop',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"'
            )
        ));
        
        $id = DB::table('menus')->where('code', 'ec-shop')->pluck('id');
        DB::table('menus')->insert(array(
            array(
                'code' => 'ec-shop-list',
                'title' => 'Shop list',
                'parent' => $id,
                'group' => 'admin-menu',
                'path' => 'shop/list',
                'anchor_attributes' => null,
                'li_attributes' => null
            )
        ));
        
        $saleman = DB::table('roles')->where('title', 'Salesman')->pluck('id');
        DB::table('access')->insert(array(
            array(
                'name' => 'Ec Shop: list',
                'class' => null,
                'role' => '3|4'
            ),
            array(
                'name' => 'Ec Shop: create',
                'class' => '\Kalephan\Ec\Shop\ShopEntity@allowCreate',
                'role' => '2'
            ),
            array(
                'name' => 'Ec Shop: read',
                'class' => null,
                'role' => '1|2'
            ),
            array(
                'name' => 'Ec Shop: update',
                'class' => null,
                'role' => '3|4'
            ),
            array(
                'name' => 'Ec Shop: own update',
                'class' => '\Kalephan\Ec\Shop\ShopEntity@isOwn',
                'role' => $saleman
            ),
            array(
                'name' => 'Ec Shop: delete',
                'class' => null,
                'role' => '3'
            ),
            array(
                'name' => 'Ec Shop: own delete',
                'class' => '\Kalephan\Ec\Shop\ShopEntity@isOwn',
                'role' => $saleman
            )
        ));
        
        lks_config_set('vendor version lks.ec-shop', 0.01);
    }
}
