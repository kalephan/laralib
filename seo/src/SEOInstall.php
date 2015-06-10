<?php
namespace Kalephan\SEO;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SEOInstall
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
        // lks_config_set('vendor version lks.seo', self::VERSION);
    }

    private static function up_0_01()
    {
        DB::table('events')->insert(array(
            array(
                'name' => 'form.fieldValidate.textarea',
                'class' => '\Kalephan\SEO\SEOEvent@fieldValidateTextarea'
            )
        ));
        
        lks_config_set('image lazy load', 1);
        
        lks_config_set('vendor version lks.seo', 0.01);
    }
}
