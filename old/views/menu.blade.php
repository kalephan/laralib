<ul class="nav navbar-nav">@if (count($menu)) {{
	lks_render('menu-onelevel', array('menu' => $menu)) }} @endif
</ul>