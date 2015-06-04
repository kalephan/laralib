<?php
namespace Kalephan\Article;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArticleInstall {

    const VERSION = 0.01;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        //lks_config_set('vendor version lks.article', self::VERSION);
    }

    private static function up_0_01() {
        Schema::create('articles', function($table) {
            $table->increments('id');
            $table->string('title', 256);
            $table->string('image', 256)->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('active')->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
        });

        DB::table('access')->insert(array(
            array(
                'name' => 'Article: show list',
                'class' => null,
                'role' => '3',
            ),
            array(
                'name' => 'Article: create',
                'class' => null,
                'role' => '3',
            ),
            array(
                'name' => 'Article: clone',
                'class' => null,
                'role' => '3',
            ),
            array(
                'name' => 'Article: own clone',
                'class' => '\Kalephan\Article\ArticleEntity@isOwn',
                'role' => '2|3',
            ),
            array(
                'name' => 'Article: update',
                'class' => null,
                'role' => '3',
            ),
            array(
                'name' => 'Article: own update',
                'class' => '\Kalephan\Article\ArticleEntity@isOwn',
                'role' => '2|3',
            ),
            array(
                'name' => 'Article: delete',
                'class' => null,
                'role' => '3',
            ),
            array(
                'name' => 'Article: own delete',
                'class' => '\Kalephan\Article\ArticleEntity@isOwn',
                'role' => '2|3',
            ),
            array(
                'name' => 'Article: view',
                'class' => null,
                'role' => '1|2',
            ),
            array(
                'name' => 'Article: own view',
                'class' => '\Kalephan\Article\ArticleEntity@isOwn',
                'role' => '2|3',
            ),
        ));

        DB::table('menus')->insert(array(
            array(
                'code' => 'article',
                'title' => 'Article',
                'parent' => 1,
                'group' => 'admin-menu',
                'path' => 'article/list',
                'anchor_attributes' => null,
                'li_attributes' => null,
            ),
        ));

        $support = config('lks.urlalias support', []);
        $support[] = 'lks-article-article-showcreateform';
        $support[] = 'lks-article-article-showupdateform';
        lks_config_set('urlalias support', $support);

        $support = config('lks.urlalias support alter value', []);
        $support[] = 'lks-article-article-showupdateform';
        lks_config_set('urlalias support alter value', $support);

        lks_config_set('vendor version lks.article', 0.01);
    }
}

/*
        $support = config('lks.lks metadata support', [], true);
        $support[] = 'lks-article-article-showcreateform';
        $support[] = 'lks-article-article-showupdateform';
        lks_config_set('lks metadata support', $support);

        $support = config('lks.lks metadata support alter value', [], true;
        $support[] = 'lks-article-article-showupdateform';
        lks_config_set('lks metadata support alter value', $support);*/
