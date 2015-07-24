<?php
namespace Kalephan\TinyMCE;

use Illuminate\Support\Facades\DB;

class TinyMCEInstall
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
        // lks_config_set('vendor version lks.tinymce', self::VERSION);
    }

    private static function up_0_01()
    {
        DB::table('events')->insert(array(
            array(
                'name' => 'form.RTEEnable',
                'class' => '\Kalephan\TinyMCE\TinyMCEEvent@buildItemTextarea',
                'weight' => 0
            )
        ));
        
        lks_config_set('tinymce libraries path', '//tinymce.cachefly.net/4.1/tinymce.min.js');
        lks_config_set('tinymce config mode', 'exact');
        lks_config_set('tinymce config theme', 'modern');
        lks_config_set('tinymce config language', 'vi');
        lks_config_set('tinymce config language_url ', '/assets/libraries/tinymce/langs/vi.js');
        lks_config_set('tinymce config relative_urls', 'false');
        lks_config_set('tinymce config width', '100%');
        lks_config_set('tinymce config height', 300);
        lks_config_set('tinymce config plugins line 1', 'advlist autolink link image lists charmap hr anchor');
        lks_config_set('tinymce config plugins line 2', 'searchreplace wordcount visualblocks visualchars');
        lks_config_set('tinymce config plugins line 3', 'table contextmenu directionality emoticons paste textcolor code');
        lks_config_set('tinymce config toolbar1', 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ');
        lks_config_set('tinymce config toolbar2', '| link unlink anchor | image emoticons charmap | forecolor backcolor | styleselect | visualblocks | code');
        lks_config_set('tinymce config image_advtab', 'true');
        lks_config_set('tinymce config external_plugins', []);
        
        lks_config_set('vendor version lks.tinymce', 0.01);
    }
}
