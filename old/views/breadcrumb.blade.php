@if (!empty($breadcrumb))
    {{ lks_template_item_list($breadcrumb, 1, ['class' => 'breadcrumb']); }}
@endif