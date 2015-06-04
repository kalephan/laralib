<?php
namespace Chovip\Chovip14;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Chovip14Install {

    const VERSION = 0.0105;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        DB::table('blocks')->insert(array(
            array(
                'delta' => 'topic-create-process-bar',
                'title' => 'Topic create process bar',
                'cache' => 'page',
                'region' => 'content',
                'class' => '\Chovip\Chovip14\Chovip14Block@blockEcTopicCreateProcessBar',
                'access' => '\Chovip\Chovip14\Chovip14Block@blockEcTopicCreateProcessBarAccess',
                'weight' => 0,
                'active' => 1,
            ),
        ));

        lks_config_set('vendor version chovip.fe15', self::VERSION);
    }

    private static function up_0_01() {
        DB::table('blocks')->insert(array(
            array(
                'delta' => 'fe15-header-home',
                'title' => 'fe15-header-home',
                'cache' => 'page',
                'region' => 'header',
                'class' => '\Chovip\Chovip14\Chovip14Block@headerHome',
                'access' => '\Chovip\Chovip14\Chovip14Block@headerHomeAccess',
                'weight' => 0,
                'active' => 1,
            ),
            array(
                'delta' => 'fe15-contenttop-home',
                'title' => 'fe15-contenttop-home',
                'cache' => 'full',
                'region' => 'content top',
                'class' => '\Chovip\Chovip14\Chovip14Block@contentTopHome',
                'access' => '\Chovip\Chovip14\Chovip14Block@contentTopHomeAccess',
                'weight' => 0,
                'active' => 1,
            ),
            array(
                'delta' => 'fe15-contentbottom-info',
                'title' => 'fe15-contentbottom-info',
                'cache' => 'full',
                'region' => 'content bottom',
                'class' => '\Chovip\Chovip14\Chovip14Block@contentBottomInfo',
                'access' => '\Chovip\Chovip14\Chovip14Block@contentBottomInfoAccess',
                'weight' => 0,
                'active' => 0,
            ),
            array(
                'delta' => 'fe15-contentbottom-product',
                'title' => 'fe15-contentbottom-product',
                'cache' => 'full',
                'region' => 'content bottom',
                'class' => '\Chovip\Chovip14\Chovip14Block@contentBottomProduct',
                'access' => '\Chovip\Chovip14\Chovip14Block@contentBottomProductAccess',
                'weight' => 1,
                'active' => 1,
            ),
            array(
                'delta' => 'fe15-contentbottom-menu',
                'title' => 'fe15-contentbottom-menu',
                'cache' => 'full',
                'region' => 'content bottom',
                'class' => '\Chovip\Chovip14\Chovip14Block@contentBottomMenu',
                'access' => '\Chovip\Chovip14\Chovip14Block@contentBottomMenuAccess',
                'weight' => 2,
                'active' => 1,
            ),
            array(
                'delta' => 'fe15-footer',
                'title' => 'fe15-footer',
                'cache' => 'full',
                'region' => 'footer',
                'class' => '\Chovip\Chovip14\Chovip14Block@footer',
                'access' => '\Chovip\Chovip14\Chovip14Block@footerAccess',
                'weight' => 0,
                'active' => 1,
            ),
            array(
                'delta' => 'fe15-scroll',
                'title' => 'fe15-scroll',
                'cache' => 'full',
                'region' => 'tags',
                'class' => '\Chovip\Chovip14\Chovip14Block@scroll',
                'access' => '\Chovip\Chovip14\Chovip14Block@scrollAccess',
                'weight' => 99,
                'active' => 0,
            ),
            array(
                'delta' => 'fe15-userpanel-left',
                'title' => 'fe15-userpanel-left',
                'cache' => 'user',
                'region' => 'left sidebar',
                'class' => '\Chovip\Chovip14\Chovip14Block@leftUserPanel',
                'access' => '\Chovip\Chovip14\Chovip14Block@leftUserPanelAccess',
                'weight' => 0,
                'active' => 1,
            ),
            array(
                'delta' => 'fe15-article-left-sidebar',
                'title' => 'fe15-article-left-sidebar',
                'cache' => 'full',
                'region' => 'left sidebar',
                'class' => '\Chovip\Chovip14\Chovip14Block@leftArticle',
                'access' => '\Chovip\Chovip14\Chovip14Block@leftArticleAccess',
                'weight' => 0,
                'active' => 1,
            ),
            array(
                'delta' => 'shop-register-guide',
                'title' => 'shop-register-guide',
                'cache' => 'full',
                'region' => 'left sidebar',
                'class' => '\Chovip\Chovip14\Chovip14Block@blockEcShopRegisterGuide',
                'access' => '\Chovip\Chovip14\Chovip14Block@blockEcShopRegisterAccess',
                'weight' => 0,
                'active' => 1,
            ),
            array(
                'delta' => 'shop-register-process-bar',
                'title' => 'shop-register-process-bar',
                'cache' => 'page',
                'region' => 'content',
                'class' => '\Chovip\Chovip14\Chovip14Block@blockEcShopRegisterProcessBar',
                'access' => '\Chovip\Chovip14\Chovip14Block@blockShopEcRegisterAccess',
                'weight' => 0,
                'active' => 1,
            ),
        ));

        lks_config_set("views dir frontend", base_path() . '/themes/fe15');
        lks_config_set("views dir userpanel", base_path() . '/themes/fe15');

        lks_config_set('vendor version chovip.fe15', 0.01);
    }


}
