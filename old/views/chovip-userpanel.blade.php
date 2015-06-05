@if (!\Auth::id())
<li class="no-device">{{fe15_fancybox_modal('{frontend}/iframe/user/login',
	'<i class="icon_dangnhap"></i>Đăng Nhập', ['data-fancybox-maxheight' =>
	307])}}
</li>
<li class="no-device">{{fe15_fancybox_modal('{frontend}/iframe/user/register',
	'<i class="icon_dangky"></i>Đăng Ký', ['data-fancybox-maxheight' =>
	440])}}
</li>
<li class="only-device">{{lks_anchor('user/login', '<i
	class="icon_dangnhap"></i>Đăng Nhập')}}
</li>
<li class="only-device">{{lks_anchor('user/register', '<i
	class="icon_dangky"></i>Đăng Ký')}}
</li>
@else
<li class="dropdown"><a
	href="{{lks_url('{userpanel}/profile/me/update')}}"
	class="dropdown-toggle active" data-toggle="dropdown"><i
		class="icon_user"></i>{{ $username }}</a>
<ul id="user_panel" class="dropdown-menu" role="menu">
		@if ($shop)
		<li class="dropdown-header">QUẢN LÝ SHOP</li> @foreach ($shop as $key
		=> $value)
		<li class="divider"></li>
		<li><a href="{{lks_url('{userpanel}/' . $key)}}" class="bullet">{{$value}}</a></li>
		@endforeach @endif
		<li class="divider"></li>
		<li class="dropdown-header">QUẢN LÝ THÔNG TIN</li>
		<li class="divider"></li>
		<li><a href="{{lks_url('{userpanel}/profile/me/update')}}"
			class="bullet">Thông Tin Cá Nhân</a></li>
		<li class="divider"></li>
		<li><a href="{{lks_url('{userpanel}/user/changepass')}}"
			class="bullet">Thay Đổi Mật Khẩu</a></li>
		<li class="divider"></li>
		<li><a href="{{lks_url('{userpanel}/order/list/me')}}" class="bullet">Đơn
				Hàng Của Tôi</a></li> @if (!$shop)
		<li class="divider"></li>
		<li><a href="{{lks_url('{userpanel}/shop/create')}}" class="bullet">Đăng
				Ký Mở Shop</a></li>@endif
		<li class="divider"></li>
		<li><a href="{{lks_url('{frontend}/user/logout')}}" class="bullet">Đăng
				Xuất</a></li>
	</ul></li>
@endif
