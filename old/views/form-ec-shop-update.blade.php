<div id="shop_update">
    @if(count($form->error))
        <div class="message_delete clearfix"><span>
            {{ lks_template_item_list($form->error); }}
        </span></div>
    @endif

    {{ lks_form_open($form->form) }}

    <div class="tabMenu"><ul>
        <li id="tab-title-1" data-tab="1" class="tab-title"><a href="#" class="active">THÔNG TIN SHOP</a></li>
        <li id="tab-title-2" data-tab="2" class="tab-title"><a href="#">PHƯƠNG THỨC THANH TOÁN</a></li>
        <li id="tab-title-3" data-tab="3" class="tab-title"><a href="#">PHƯƠNG THỨC GIAO HÀNG</a></li>
        <li id="tab-title-4" data-tab="4" class="tab-title"><a href="#">GIỚI THIỆU</a></li>
        <li id="tab-title-5" data-tab="5" class="tab-title"><a href="#">LIÊN HỆ</a></li>
    </ul></div>

    <div id="tab-content-1" class="tab-content box_content border-top-none padd60 mar_bottom7">
        {{ lks_form_render_all($form['#group_shop']) }}
    </div>

    <div id="tab-content-2" class="tab-content box_content border-top-none padd60 mar_bottom7 hideMe">
        {{ lks_form_render_all($form['#group_paymenth']) }}
    </div>

    <div id="tab-content-3" class="tab-content box_content border-top-none padd60 mar_bottom7 hideMe">
        {{ lks_form_render_all($form['#group_shipmenth']) }}
    </div>

    <div id="tab-content-4" class="tab-content box_content border-top-none padd60 mar_bottom7 hideMe">
        {{ lks_form_render_all($form['#group_aboutus']) }}
    </div>

    <div id="tab-content-5" class="tab-content box_content border-top-none padd60 mar_bottom7 hideMe">
        {{ lks_form_render_all($form['#group_contact']) }}
    </div>

    {{-- show actions at the end of form --}}
    <div class="form_action_buttons clearfix">
        {{ lks_form_render_all($form->actions) }}
    </div>

    {{ lks_form_render_all($form) }}
    {{ lks_form_close() }}
</div>