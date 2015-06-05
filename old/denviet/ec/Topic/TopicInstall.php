<?php
namespace Kalephan\Ec\Topic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TopicInstall
{

    const VERSION = 0.0101;

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
        $saleman = DB::table('roles')->where('title', 'Salesman')->pluck('id');
        DB::table('access')->insert(array(
            array(
                'name' => 'Ec Topic: show list',
                'class' => null,
                'role' => "3"
            ),
            array(
                'name' => 'Ec Topic: show own list',
                'class' => '\Kalephan\Ec\Topic\TopicEntity@isOwn',
                'role' => "3|$saleman"
            ),
            array(
                'name' => 'Ec Topic: create',
                'class' => null,
                'role' => "3|$saleman"
            ),
            array(
                'name' => 'Ec Topic: clone',
                'class' => null,
                'role' => "3"
            ),
            array(
                'name' => 'Ec Topic: own clone',
                'class' => '\Kalephan\Ec\Topic\TopicEntity@isOwn',
                'role' => "3|$saleman"
            ),
            array(
                'name' => 'Ec Topic: view',
                'class' => null,
                'role' => null
            ),
            array(
                'name' => 'Ec Topic: own view',
                'class' => '\Kalephan\Ec\Topic\TopicEntity@isOwn',
                'role' => "3|$saleman"
            ),
            array(
                'name' => 'Ec Topic: delete',
                'class' => null,
                'role' => "3"
            ),
            array(
                'name' => 'Ec Topic: own delete',
                'class' => '\Kalephan\Ec\Topic\TopicEntity@isOwn',
                'role' => "3|$saleman"
            ),
            array(
                'name' => 'Ec Topic: update',
                'class' => null,
                'role' => 3
            ),
            array(
                'name' => 'Ec Topic: own update',
                'class' => '\Kalephan\Ec\Topic\TopicEntity@isOwn',
                'role' => $saleman
            )
        ));
        
        lks_config_set('vendor version lks.ec-topic', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::create('ec_topics', function ($table) {
            $table->bigIncrements('id');
            $table->string('title', 256);
            $table->string('short_desc', 256)->nullable();
            $table->text('content')->nullable();
            $table->integer('price')->nullable();
            $table->string('image', 256)->nullable();
            $table->text('products')->nullable();
            $table->integer('created_by')
                ->unsigned()
                ->nullable();
            $table->integer('updated_by')
                ->unsigned()
                ->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->boolean('active')->default(0);
            
            $table->index('price');
        });
        
        /*
         * DB::table('menus')->insert(array(
         * array(
         * 'title' => 'Danh sách Ec topic',
         * 'path' => 'topic/list',
         * 'arguments' => 'all',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showList',
         * 'access' => 'Ec Topic: show list',
         * ),
         * array(
         * 'title' => 'Danh sách Ec topic',
         * 'path' => 'topic/shop/list',
         * 'arguments' => '1',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showList',
         * 'access' => 'Ec Topic: show own list',
         * ),
         * array(
         * 'title' => 'Tạo Ec topic',
         * 'path' => 'topic/create',
         * 'arguments' => '',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showCreate',
         * 'access' => 'Ec Topic: create',
         * ),
         * array(
         * 'title' => 'Sao chép Ec topic',
         * 'path' => 'topic/%/clone',
         * 'arguments' => '1',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showClone',
         * 'access' => 'Ec Topic: clone|Ec Topic: own clone',
         * ),
         * array(
         * 'title' => 'Xem Ec topic',
         * 'path' => 'topic/%',
         * 'arguments' => '1',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showRead',
         * 'access' => 'Ec Topic: view|Ec Topic: own view',
         * ),
         * array(
         * 'title' => 'Cập nhật Ec topic',
         * 'path' => 'topic/%/update',
         * 'arguments' => '1',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showUpdate',
         * 'access' => 'Ec Topic: update|Ec Topic: own update',
         * ),
         * array(
         * 'title' => 'Xóa Ec topic',
         * 'path' => 'topic/%/delete',
         * 'arguments' => '1',
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showDelete',
         * 'access' => 'Ec Topic: delete|Ec Topic: own delete',
         * ),
         * array(
         * 'title' => 'Xóa một field của Topic',
         * 'path' => 'topic/%id/empty-field/image',
         * 'arguments' => "1|3",
         * 'class' => '\Kalephan\Ec\Topic\TopicEntity',
         * 'method' => 'showEmptyField',
         * 'access' => 'Ec Topic: update|Ec Topic: own update',
         * ),
         * ));
         */
        
        /*
         * $menutree_ec_id = \DB::table('menutree')->where('code', 'shop')->pluck('id');
         * DB::table('menutree')->insert(array(
         * array(
         * 'code' => 'ec-topic',
         * 'title' => 'Ec topic',
         * 'parent' => $menutree_ec_id,
         * 'group' => 'admin-menu',
         * 'path' => 'topic/list',
         * 'anchor_attributes' => null,
         * 'li_attributes' => null,
         * ),
         * ));
         */
        
        lks_config_set('vendor version lks.ec-topic', 0.01);
    }
}
