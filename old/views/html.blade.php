<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	@section('title') <title>{{Output::title()}}</title> @show
    @section('css') {!!Asset::css()!!} @show
    @section('head') {{Output::head()}} @show

	<!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
    <![endif]-->
</head>
<body>

@section('header') @include('block-header') @show

<div class="container">
  <div class="row">
	<div class="col-sm-3"> @section('left-sidebar') @include('block-left-sidebar') @show </div>
    <div class="col-sm-9">
        @section('message') {!!Output::message()!!} @show
        @yield('content')
    </div>
  </div>

  @section('content-bottom') @include('block-content-bottom') @show
</div>


<footer class="text-center"> @section('footer') @include('block-footer') @show </footer>

@section('modal') @include('block-modal') @show
@section('closure') {{Output::closure()}} @show
@section('js') {!!Asset::js()!!} @show
</body>
</html>