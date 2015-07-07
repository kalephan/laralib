@extends('html')

@section('content')
    @if (!empty($structure->actions['add']))
        <div class="btn-group" role="group" aria-label="...">
            <a href="{{lks_url(lks_entity_token_trans($structure->actions['add']['url'], null, $structure))}}" class="btn btn-default">{{$structure->actions['add']['title']}}</a>
        </div>
    @endif

    @if (count($entities))
        {!!lks_table(lks_entities2table($entities, $structure))!!}
        {!!$paginator->render()!!}
    @else
        {{lks_lang('Không tìm thấy kết quả.')}}
    @endif
@endsection
