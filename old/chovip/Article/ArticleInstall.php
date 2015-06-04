<?php
namespace Chovip\Article;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArticleInstall {

    const VERSION = 0.01;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    //Target: 27-12-2014
    private static function up_0_01x() {
        //lks_config_set('vendor version chovip.article', self::VERSION);
    }

    private static function up_0_01() {
        Schema::table('articles', function($table) {
            $table->integer('category_id')->unsigned()->nullable();
        });

        DB::table('events')->insert(array(
            array(
                'name' => 'entity.structureAlter.articles',
                'class' => '\Chovip\Article\ArticleEvent@structureAlterArticle',
            ),
        ));

        lks_config_set('vendor version chovip.article', 0.01);
    }
}
