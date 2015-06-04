<?php
namespace Chovip\Profile;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileInstall {

    const VERSION = 0.01;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        //lks_config_set('vendor version chovip.profile', self::VERSION);
    }

    private static function up_0_01() {
        Schema::table('profiles', function($table) {
            $table->integer('province_id')->unsigned()->nullable();
            $table->integer('district_id')->unsigned()->nullable();
            $table->string('mobile', 32)->nullable();
            $table->string('cmnd', 16)->nullable();
            $table->string('homephone', 32)->nullable();
        });

        DB::table('events')->insert(array(
            array(
                'name' => 'entity.structureAlter.profiles',
                'class' => '\Chovip\Profile\ProfileEvent@alterEntityStructureProfile',
            ),
            array(
                'name' => 'form.formAlter.lks-profile-profileentity-showupdateform',
                'class' => '\Chovip\Profile\ProfileEvent@alterShowUpdateForm',
            ),
            array(
                'name' => 'form.formValueAlter.lks-profile-profileentity-showupdateform',
                'class' => '\Chovip\Profile\ProfileEvent@alterShowUpdateFormValue',
            ),
        ));

        lks_config_set('vendor version chovip.profile', 0.01);
    }


}
