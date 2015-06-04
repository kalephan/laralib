<div id="user_login">
    {{ lks_form_open($form->form) }}
    <div class="modal-header">
        <h4 class="modal-title">{{ lks_page_title() }}</h4>
    </div>

    <div class="modal-body"><div class="te">
        @if(count($form->error))
            <div class="bg-danger">{{ implode('<br />', $form->error) }}</div>
        @endif

        {{ lks_form_render_all($form) }}
    </div></div>

    <div class="modal-footer">
        <div class="forgotpass_links">
            {{ fe15_fancybox_modal('{frontend}/iframe/user/forgotpass', lks_lang('Quên mật khẩu?'), ['data-fancybox-maxheight' => 185]) }}
            |
            {{ fe15_fancybox_modal('{frontend}/iframe/user/activation/resend', lks_lang('Chưa nhận được email xác nhận?'), ['data-fancybox-maxheight' => 185]) }}
        </div>

        <div class="action_buttons">
            {{ lks_form_render_all($form->actions) }}
            {{--
            @if ($social_facebook = lks_instance_get()->load('\Kalephan\SocialFb\SocialFb'))
            <div class="login_button_text"> Hoặc Đăng Nhập Bằng </div>
            {{ lks_anchor($social_facebook->getLoginUrl(), '<div class="login_button_fb"></div>', ['class' => 'icon_face']) }}
            @endif
            --}}

            <div class="register_text">Bạn chưa có tài khoản,
            {{ fe15_fancybox_modal('{frontend}/iframe/user/register', lks_lang('bấm vào đây'), ['data-fancybox-maxheight' => 440]) }}
             để đăng ký</div>
        </div>
    </div>

    {{ lks_form_close() }}
</div>
