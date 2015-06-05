<?php
namespace Kalephan\Mobile\ESMS;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ESMSInstall
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
    
    // Target: 27-12-2014
    private static function up_0_01x()
    {
        // lks_config_set('vendor version lks.esms', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::create('esms', function ($table) {
            $table->increments('id');
            $table->string('smsid', 256)->nullable();
            $table->text('data')->nullable();
            $table->dateTime('created_at')->nullable();
            
            $table->index('smsid');
        });
        
        // Insert Default Data
        /*
         * DB::table('menus')->insert(array(
         * array(
         * 'title' => 'ESMS list',
         * 'path' => 'esms/list',
         * 'arguments' => '',
         * 'class' => '\Kalephan\Mobile\ESMS\ESMSEntity',
         * 'method' => 'showList',
         * 'access' => 'ESMS: show list',
         * ),
         * array(
         * 'title' => 'ESMS read',
         * 'path' => 'esms/%',
         * 'arguments' => '1',
         * 'class' => '\Kalephan\Mobile\ESMS\ESMSEntity',
         * 'method' => 'showRead',
         * 'access' => 'ESMS: view',
         * ),
         * array(
         * 'title' => 'ESMS',
         * 'path' => 'esms',
         * 'arguments' => '',
         * 'class' => '\Kalephan\Mobile\ESMS\ESMSEntity',
         * 'method' => 'showTracking',
         * 'access' => null,
         * ),
         * ));
         *
         * $id = DB::table('menutree')
         * ->where('code', 'chovip')
         * ->pluck('id');
         *
         * DB::table('menutree')->insert(array(
         * array(
         * 'code' => 'esms',
         * 'title' => 'ESMS',
         * 'parent' => $id,
         * 'group' => 'admin-menu',
         * 'path' => 'esms/list',
         * 'anchor_attributes' => null,
         * 'li_attributes' => null,
         * ),
         * ));
         *
         * DB::table('access')->insert(array(
         * array(
         * 'name' => 'ESMS: show list',
         * 'class' => '',
         * 'method' => '',
         * ),
         * array(
         * 'name' => 'ESMS: view',
         * 'class' => '',
         * 'method' => '',
         * ),
         * ));
         *
         * lks_add_role_to_access('ESMS: show list', array(3, 4));
         * lks_add_role_to_access('ESMS: view', array(3, 4));
         */
        
        lks_config_set('vendor version lks.esms', 0.01);
    }
}
