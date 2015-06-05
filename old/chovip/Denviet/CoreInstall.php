<?php
namespace Chovip\LKS;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CoreInstall
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
        lks_config_set('vendor version chovip.lks', self::VERSION);
    }

    private static function up_0_01()
    {
        DB::table('events')->insert(array(
            array(
                'name' => 'entity.structureAlter.users',
                'class' => '\Chovip\Kalephan\UserEvent@structureAlterUser'
            ),
            array(
                'name' => 'form.formAlter.lks-user-userentity-showregisterform',
                'class' => '\Chovip\Kalephan\UserEvent@formAlterRegister'
            ),
            array(
                'name' => 'form.formAlter.lks-user-userentity-showloginform',
                'class' => '\Chovip\Kalephan\UserEvent@formAlterLogin'
            ),
            array(
                'name' => 'form.formAlter.lks-user-useractivationcontroller-showuseractivationresendform',
                'class' => '\Chovip\Kalephan\UserEvent@formAlterUserActivationResend'
            ),
            array(
                'name' => 'form.formAlter.lks-user-userentity-showforgotpasswordform',
                'class' => '\Chovip\Kalephan\UserEvent@formAlterForgotPassword'
            ),
            array(
                'name' => 'form.formAlter.lks-user-userentity-showresetpasswordform',
                'class' => '\Chovip\Kalephan\UserEvent@formAlterResetPassword'
            )
        ));
        
        lks_config_set('vendor version chovip.lks', 0.01);
    }
}
