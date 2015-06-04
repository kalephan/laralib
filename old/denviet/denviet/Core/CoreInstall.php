<?php
namespace Kalephan\Core;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CoreInstall {

    const VERSION = 0.0101;

    public static function up($prev) {
        if ($prev < 0.01 && $prev < self::VERSION) { self::up_0_01(); }
        if ($prev < self::VERSION) { self::up_0_01x(); }
    }

    // Target: 01-01-2015
    private static function up_0_01x() {
        lks_config_set('lks site backend', 'backend');
        lks_config_set('lks site userpanel', 'userpanel');

        lks_config_set('vendor version lks.core', self::VERSION);
    }

    private static function up_0_01() {
        // ------------ CREATE TABLES ------------
        Schema::create('variables', function ($table) {
            $table->string('name', 128);
            $table->text('value')->nullable();

            $table->primary('name');
        });

        Schema::create('events', function ($table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('class', 256);
            $table->tinyInteger('weight')->default(0);
        });

        Schema::create('blocks', function ($table) {
            $table->increments('id');
            $table->string('delta', 256)->nullable();
            $table->string('title', 256);
            $table->string('cache', 32)->nullable();
            $table->string('region', 256)->nullable();
            $table->string('class', 256)->nullable();
            $table->string('access', 256)->nullable();
            $table->tinyInteger('weight')->default(0);
            $table->boolean('active')->default(1);
        });

        Schema::create('styles', function ($table) {
            $table->string('style', 32);
            $table->smallInteger('width')->nullable();
            $table->smallInteger('height')->nullable();
            $table->string('type', 32)->default('scale');
            $table->boolean('is_upsize')->default(0);

            $table->primary('style');
        });

        Schema::create('menus', function($table) {
            $table->increments('id');
            $table->string('code', 32)->nullable();
            $table->string('title', 256)->nullable();
            $table->integer('parent')->unsigned()->nullable();
            $table->string('group', 32);
            $table->string('path', 256)->nullable();
            $table->text('anchor_attributes', 256)->nullable();
            $table->text('li_attributes', 256)->nullable();
            $table->tinyInteger('weight')->default(0);
            $table->boolean('active')->default(1);

            $table->index('code');
            $table->index('group');
            $table->index('path');
        });

        Schema::create('trans', function ($table) {
            $table->text('original');
        });

        Schema::create('otl', function ($table) {
            $table->increments('id');
            $table->string('hash', 128);
            $table->integer('destination')->unsigned()->nullable();
            $table->dateTime('expired')->nullable();
            $table->string('type', 32)->nullable();

            $table->index('hash');
        });

        Schema::create('urlalias', function ($table) {
            $table->bigIncrements('id');
            $table->string('real', 256);
            $table->string('alias', 256);

            $table->index('real');
            $table->index('alias');
        });

        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('fullname', 128)->nullable();
            $table->string('username', 128)->nullable();
            $table->string('email', 128)->nullable();
            $table->string('password', 128)->nullable();
            $table->boolean('active')->default(0);
            $table->rememberToken();
            $table->dateTime('last_activity')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('username');
            $table->index('email');
        });

        Schema::create('roles', function ($table) {
            $table->increments('id');
            $table->string('title', 64);
            $table->tinyInteger('weight')->default(0);
            $table->boolean('active')->default(1);
        });

        Schema::create('users_roles', function ($table) {
            $table->string('field', 32);
            $table->integer('users_id')->unsigned();
            $table->integer('roles_id')->unsigned();
        });

        Schema::create('access', function ($table) {
            $table->string('name', 64);
            $table->string('class', 256)->nullable();
            $table->string('role', 256)->nullable();

            $table->primary('name');
        });

        // ------------ INSERT DATA ------------

        DB::table('events')->insert(array(
            array(
                'name' => 'entity.createFormSubmit.file',
                'class' => '\Kalephan\Core\FieldEvent@createFormSubmitFile',
                'weight' => 0,
            ),
            array(
                'name' => 'helpers.makeURLAlter',
                'class' => '\Kalephan\Core\CoreEvent@makeURLAlterUACP',
                'weight' => -127,
            ),
            array(
                'name' => 'request.uriAlter',
                'class' => '\Kalephan\Core\CoreEvent@uriAlterUACP',
                'weight' => -127,
            ),
            array(
                'name' => 'entity.showReadExecutive.file',
                'class' => '\Kalephan\Core\FieldEvent@showReadExecutiveFile',
                'weight' => 0,
            ),
            array(
                'name' => 'entity.createEntityAfter.users',
                'class' => '\Kalephan\User\UserActivationEvent@sendUserActivationEmail',
                'weight' => 0,
            ),
            array(
                'name' => 'request.uriAlter',
                'class' => '\Kalephan\URLAlias\URLAliasEvent@uriAlterAlias',
                'weight' => 0,
            ),
            array(
                'name' => 'helpers.makeURLAlter',
                'class' => '\Kalephan\URLAlias\URLAliasEvent@makeURLAlterAlias',
                'weight' => 0,
            ),
            array(
                'name' => 'form.formAlter',
                'class' => '\Kalephan\URLAlias\URLAliasEvent@formAlterAddURLAlias',
                'weight' => 0,
            ),
            array(
                'name' => 'form.formValueAlter',
                'class' => '\Kalephan\URLAlias\URLAliasEvent@formValueAlterAddURLAlias',
                'weight' => 0,
            ),
        ));

        DB::table('styles')->insert(array(
            array(
                'style' => 'normal',
                'width' => 450,
                'height' => 300,
                'type' => 'scale',
            ),
            array(
                'style' => 'thumbnail',
                'width' => 100,
                'height' => 100,
                'type' => 'scale',
            ),
        ));

        DB::table('blocks')->insert(array(
            array(
                'delta' => 'adminmenu',
                'title' => 'Administrator menu bar',
                'cache' => 'user',
                'region' => 'admin menu',
                'class' => '\Kalephan\Menu\MenuBlock@blockMenu@admin-menu',
                'access' => '\Kalephan\Menu\MenuBlock@blockMenuAdminAccess',
                'weight' => 0,
                'active' => 1,
            ),
        ));

        DB::table('menus')->insert(array(
            array(
                'code' => 'content',
                'title' => 'Nội dung',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => -99,
            ),
            array(
                'code' => 'structure',
                'title' => 'Kiến trúc',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => -98,
            ),
            array(
                'code' => 'people',
                'title' => 'Thành viên',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => -97,
            ),
            array(
                'code' => 'configuration',
                'title' => 'Cấu hình',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => -96,
            ),
            array(
                'code' => 'seo',
                'title' => 'Seo',
                'parent' => null,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => -95,
            ),
            array(
                'code' => 'performance',
                'title' => 'Hiệu suất',
                'parent' => 4,
                'group' => 'admin-menu',
                'path' => '#',
                'anchor_attributes' => 'class="dropdown-toggle" data-toggle="dropdown"',
                'li_attributes' => 'class="dropdown"',
                'weight' => 0,
            ),
        ));

        $now = date('Y-m-d H:i:s');
        DB::table('users')->insert(array(
            array(
                'id' => 1,
                'fullname' => 'I am Admin',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'last_activity' => $now,
            ),
        ));

        DB::table('roles')->insert(array(
            array(
                'title' => 'Anonymous User',
            ),
            array(
                'title' => 'Registered User',
            ),
            array(
                'title' => 'Administrator',
            ),
            array(
                'title' => 'Editor',
            ),
        ));

        DB::table('access')->insert(array(
            array(
                'name' => 'Content: normal',
                'class' => null,
                'role' => '1|2',
            ),
            array(
                'name' => 'Admin: access acp',
                'class' => null,
                'role' => '3',
            ),
            array(
                'name' => 'User: register',
                'class' => null,
                'role' => '1|3',
            ),
            array(
                'name' => 'User: login',
                'class' => null,
                'role' => '1',
            ),
            array(
                'name' => 'User: logout',
                'class' => null,
                'role' => '2',
            ),
            array(
                'name' => 'User: register full',
                'class' => null,
                'role' => '3',
            ),
        ));

        // ------------ SET CONFIGURARION ------------

        lks_config_set("assets css compressed", false);
        lks_config_set("assets js compressed", false);lks_config_set('cache expired', 10);
        lks_config_set('cdn image style make', 0);
        lks_config_set('lks queue use', 0);
        lks_config_set('file image background', '#ffffff');
        lks_config_set('file image quality', 70);
        lks_config_set('file image rule', 'mimes:jpeg,png,gif');
        lks_config_set('file path', '/files');
        lks_config_set('form error message show in field', 1);
        lks_config_set('language default', 'original');
        lks_config_set('create user need activation', 1);
        lks_config_set('create user need activation subject', 'Kích hoạt tài khoản của bạn');
        lks_config_set('create user activation resend email subject', 'Gửi lại mã kích hoạt tài khoản của bạn');
        lks_config_set('onetimelink expired', 172800);
        lks_config_set('pagination items', 5);
        lks_config_set('pagination items per page', 25);
        lks_config_set('request prefix paths', ['modal', 'ajax', 'json', 'esi', 'admin', 'up', 'iframe']);
        lks_config_set('response home path', '/');
        lks_config_set('session.lifetime', 120);
        lks_config_set('sitename', 'LKS CMS');
        lks_config_set('urlalias default prefix', null);
        lks_config_set('urlalias roles', [3]);
        lks_config_set('user forgotpass email subject', 'Thiết lập lại mật khẩu của bạn');
        lks_config_set("views dir backend", base_path() . '/vendor/lks/lks/views/admin');
        lks_config_set("views dir base", base_path() . '/vendor/lks/lks/views');
        lks_config_set("views dir frontend", base_path() . '/vendor/lks/lks/views');

        // ------------ SAVE VERSION ------------

        lks_config_set('vendor version lks.core', 0.01);
    }
}


/*
lks_config_set('url alias default prefix', 'v');

$id = DB::table('menutree')
->where('code', 'performance')
->pluck('id');

DB::table('menutree')->insert(array(
    array(
        'code' => 'performance-clear-cache',
        'title' => 'Xóa Cache',
        'parent' => $id,
        'group' => 'admin-menu',
        'path' => 'performance/clear/cache',
        'anchor_attributes' => null,
        'li_attributes' => null,
        ),
    ));
*/
