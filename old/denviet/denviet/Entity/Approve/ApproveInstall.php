<?php
namespace Kalephan\LKS\Approve;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApproveInstall
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
        // lks_config_set('vendor version lks.entity-approve', self::VERSION);
    }

    private static function up_0_01()
    {
        Schema::create('entity_approve', function ($table) {
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            
            $table->primary('key');
        });
        
        lks_config_set('vendor version lks.entity-approve', 0.01);
    }
}
