<?php
namespace Kalephan\Profile;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileInstall
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
        // lks_config_set('vendor version lks.profile.user', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::create('profiles', function ($table) {
            $table->integer('id')->unsigned();
            $table->string('gender', 16)->nullable();
            $table->dateTime('birthday')->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('created_by')
                ->unsigned()
                ->nullable();
            $table->integer('updated_by')
                ->unsigned()
                ->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            
            $table->primary('id');
            $table->index('gender');
            $table->index('birthday');
        });
        
        DB::table('access')->insert(array(
            array(
                'name' => 'Profile: update',
                'class' => null,
                'role' => '3'
            ),
            array(
                'name' => 'Profile: own update',
                'class' => '\Kalephan\User\UserEntity@isOwn',
                'role' => '2'
            )
        ));
        
        lks_config_set('vendor version lks.profile', 0.01);
    }
}
