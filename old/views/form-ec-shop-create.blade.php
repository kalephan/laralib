<div id="shop_create" class="clearfix">
    @if(count($form->error))
        <div class="message_delete clearfix"><span>
            {{ lks_template_item_list($form->error); }}
        </span></div>
    @endif

    {{ lks_form_open($form->form) }}

    <div class="tab_title_main">THÔNG TIN CHỦ SHOP</div>
    <div class="box_content border-top-none padd17">
        {{ lks_form_render_all($form['#group_owner']) }}
    </div>

    <div class="tab_title_main mar_top7"> THÔNG TIN SHOP </div>
    <div class="box_content border-top-none mar_bottom7">
        {{ lks_form_render_all($form['#group_shop']) }}
    </div>

    {{-- show actions at the end of form --}}
    <div class="form_action_buttons clearfix">
        {{ lks_form_render_all($form->actions) }}
    </div>

    {{ lks_form_render_all($form) }}
    {{ lks_form_close() }}
</div>