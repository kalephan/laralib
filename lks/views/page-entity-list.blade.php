@extends('html')

@section('content')
    @if (!empty($structure->actions['create']))
        <div class="btn-group" role="group" aria-label="...">
            <a href="{{lks_url(lks_entity_token_trans($structure->actions['create']['url'], null, $structure))}}" class="btn btn-default">{{$structure->actions['create']['title']}}</a>
        </div>
    @endif

    @if (count($entities))
        {!!lks_table(lks_entities2table($entities, $structure))!!}
        {!!$paginator->render()!!}
    @else
        {{lks_lang('Không tìm thấy kết quả.')}}
    @endif
@endsection
