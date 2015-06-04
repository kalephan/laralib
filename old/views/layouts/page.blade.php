@extends('layouts.html')

@section('body')
    <div id="header" class="container">
        {{ $regions['header'] }}
    </div>

    @if ($regions['primary menu'])
    <div id="primary_menu" class="navbar navbar-default" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse">
            {{ $regions['primary menu'] }}
        </div>
      </div>
    </div>
    @endif

    @if (!empty($breadcrumb))
        <div id="breadcrumb" class="container">{{ $breadcrumb }}</div>
    @endif

    <div id="main_content" class="container">
        @if ($regions['left sidebar'])
            <div id="sidebar_left" class="sidebar sidebar_left">
                {{ $regions['left sidebar'] }}
            </div>
        @endif

        <div id="center"><div id="squeeze" class="panel panel-default">
            @if (!empty($title))
                <div id="page_title" class="panel-heading">{{ reset($title) }}</div>
            @endif

            <div class="panel-body">
                @if($message = lks_instance_get()->response->getMessage())
                    <div id="mesages">
                        @if (isset($message['success']))
                            <div class="alert alert-success">
                                {{ implode('<br />', $message['success']) }}
                            </div>
                        @endif
                        @if (isset($message['warning']))
                            <div class="alert alert-warning">
                                {{ implode('<br />', $message['warning']) }}
                            </div>
                        @endif
                        @if (isset($message['error']))
                            <div class="alert alert-danger">
                                {{ implode('<br />', $message['error']) }}
                            </div>
                        @endif
                    </div>
                @endif

                <div id="content"> {{ $content }}</div>
            </div>
        </div></div>


        @if ($regions['right sidebar'])
            <div id="sidebar_right" class="sidebar sidebar_right">
                {{ $regions['right sidebar'] }}
            </div>
        @endif
    </div>

    @if($regions['secondary menu'])
        <div id="secondary_menu" class="container">
            {{ $regions['secondary menu'] }}
        </div>
    @endif

    @if($regions['footer'])
        <div id="footer" class="container">
            {{ $regions['footer'] }}
        </div>
    @endif

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