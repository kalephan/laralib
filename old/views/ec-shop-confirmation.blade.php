<div class="tab_title_main">XÁC NHẬN THÔNG TIN BẰNG SMS</div>
<div class="box_content border-top-none mar_bottom7">
	<p>
		Hệ thống chỉ hỗ trợ các mạng sau: <strong>Mobifone, Vinaphone, Viettel</strong>.<br>
		Số điện thoại mà bạn sử dụng để gửi tin nhắn kích hoạt shop sẽ được <strong>ChoVip.vn</strong>
		dùng làm số điện thoại liên lạc đến shop.Vì vậy, vui lòng sử dụng số
		điện thoại mà bạn dùng thường xuyên để gửi tin nhắn.<br> Phí kích hoạt
		shop thông qua SMS là <font color="#FF0000">5.000 đ</font> - sẽ được
		trừ trực tiếp vào tài khoản chính số điện thoại của bạn
	</p>
	<div class="phone_icon"></div>
	<div class="box_sms">
		<label class="tab_title_main" style="border: none">Hướng Dẫn Kích Hoạt
			Bằng SMS</label>
		<p>
			Sử dụng số điện thoại: <font>{{$mobile}}</font> và soạn tin nhắn theo
			cú pháp:
		</p>
		<div class="syntax">
			<span>ChoVip</span> {{$path}}
		</div>
		<div class="send_syntax">
			và gửi đến <font>6580</font>
		</div>
	</div>
	<div class="bt_send_syntax">
		<a href="{{lks_url('{userpanel}/shop/' . $id . '/finalize')}}"
			class="button_or bg_button_or">Đã kích hoạt SMS</a>
	</div>
</div>