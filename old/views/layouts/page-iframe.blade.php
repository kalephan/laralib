@extends('layouts.html')

@section('body')
    {{ $content }}

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"></div></div></div>
    <div class="modal fade" id="myModalOther" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"></div></div></div>
@stop



@section('title')
    @if (!empty($title))
        <title>{{ implode(' | ', $title); }}</title>
    @endif
@stop

@section('header')
    @if (!empty($header))
        {{ implode('', $header); }}
    @endif
@stop

@section('body_tag')
    <body @if (!empty($body_class)) class="{{ $body_class }}" @endif >
@stop

@section('closure')
    @if (!empty($closure))
        {{ implode('', $closure); }}
    @endif
@stop