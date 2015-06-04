{{ lks_render('menu', array('menu' => $menu)) }}

<ul class="nav navbar-nav navbar-right">
    <li><a href="{{ lks_url('{frontend}/user/logout') }}">{{ lks_lang('Đăng xuất') }}</a></li>
</ul>