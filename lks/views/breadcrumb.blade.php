@if (count($breadcrumb)) 
    {!!HTML::decode(HTML::ul($breadcrumb, ['class' => 'breadcrumb']))!!}
@endif