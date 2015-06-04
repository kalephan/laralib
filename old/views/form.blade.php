<div class="form">
    @if(count($form->error))
        <div class="form_errors alert alert-danger">
            {!! lks_form_error($form) !!}
        </div>
    @endif

    {!! lks_form_open($form->form) !!}
        {!! lks_form_render_all($form->fields) !!}

        {{-- show actions at the end of form --}}
        <div class="form_actions clearfix">
            {!! lks_form_render_all($form->actions) !!}
        </div>
    {!! lks_form_close() !!}
</div>