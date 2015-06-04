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

<div class="modal-footer action_buttons">
    {{ lks_form_render_all($form->actions) }}
</div>

{{ lks_form_close() }}