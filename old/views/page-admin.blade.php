@extends('layouts.html') @section('body') @if ($regions['admin menu'])
<div id="admin_menu" class="navbar navbar-default navbar-fixed-top"
	role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span> <span
					class="icon-bar"></span> <span class="icon-bar"></span> <span
					class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{lks_url('{backend}/')}}">{{config('lks.sitename',
				'LKS CMS')}}</a>
		</div>
		<div class="collapse navbar-collapse">{{ $regions['admin menu'] }}</div>
	</div>
</div>
@endif @if (!empty($breadcrumb))
<div id="breadcrumb" class="container">{{ $breadcrumb }}</div>
@endif

<div id="main_content" class="container">

	<div id="center">
		<div id="squeeze" class="panel panel-default">
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
					@endif @if (isset($message['warning']))
					<div class="alert alert-warning">
						{{ implode('<br />', $message['warning']) }}
					</div>
					@endif @if (isset($message['error']))
					<div class="alert alert-danger">
						{{ implode('<br />', $message['error']) }}
					</div>
					@endif
				</div>
				@endif

				<div id="content">
					{{ $regions['admin content top'] }} @if($cl =
					lks_instance_get()->response->getFlash('contextual-link'))
					<div id="contextual-link">{{$cl}}</div>
					@endif {{ $content }} {{ $regions['admin content bottom'] }}
				</div>
			</div>
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<iframe id="myModal-frame" name="myModal-frame" frameborder="0"></iframe>
		</div>
	</div>
</div>
<div class="modal fade" id="myModalOther" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
@stop @section('title') @if (!empty($title))
<title>{{ implode(' | ', $title); }}</title>
@endif @stop @section('header') @if (!empty($header)) {{ implode('',
$header); }} @endif @stop @section('body_tag')
<body @if (!empty($body_class)) class="{{ $body_class }}" @endif>@stop

	@section('closure') @if (!empty($closure)) {{ implode('', $closure); }}
	@endif @stop