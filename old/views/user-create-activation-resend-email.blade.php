<p>
        Chào bạn <b>{{ $email }}</b>!<br /><br />
        Bạn vừa nhận được yêu cầu kích hoạt lại tài khoản trên Chovip.vn<br />
        Vui lòng nhấn vào liên kết bên dưới để hoàn tất quá trình đăng ký. <br />
        Nếu bạn không nhấn được vào link vui  lòng sao chép liên kết bên dưới và dán vào trình duyệt.<br />
        {{ lks_anchor($link, $link) }}</p>
      <p><span class="styleRed1">Email này có giá trị đến hết ngày {{ date('d/m/Y H:i', strtotime($expired)); }}</span>  (ngày/tháng/năm)</p>
      <p><b>Trân trọng cám ơn quý khách,</b><br />
        Ban Quản Trị ChoVip.vn
</p>