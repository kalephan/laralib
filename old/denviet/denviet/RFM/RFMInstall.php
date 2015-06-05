<?php
namespace Kalephan\RFM;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class RFMInstall
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
        // lks_config_set('vendor version lks.rfm', self::VERSION);
    }

    private static function up_0_01()
    {
        DB::table('events')->insert(array(
            array(
                'name' => 'tinyMCE.loadConfig',
                'class' => '\Kalephan\RFM\RFMEvent@addToTinyMCE',
                'weight' => 0
            ),
            array(
                'name' => 'form.buildItem.file',
                'class' => '\Kalephan\RFM\RFMEvent@addToFileBrowse',
                'weight' => 0
            )
        ));
        
        lks_config_set('rfm tinymce config external_filemanager_path', '/rfm/filemanager/');
        lks_config_set('rfm tinymce config filemanager_title', 'Thư Viện Ảnh');
        lks_config_set('rfm tinymce config filemanager_crossdomain', 'false');
        
        $external_plugin = config('lks.tinymce config external_plugins', []);
        $external_plugin['filemanager'] = '"filemanager":"/rfm/filemanager/plugin.min.js"';
        lks_config_set('tinymce config external_plugins', $external_plugin);
        
        lks_config_set('rfm config dialog path', '/rfm/filemanager/dialog.php');
        lks_config_set('rfm config crossdomain', 0);
        
        lks_config_set('vendor version lks.rfm', 0.01);
    }
}
