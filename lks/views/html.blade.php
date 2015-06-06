<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    @section('title') <title>{{Output::title()}}</title> @show
    @section('css') {!!Asset::css()!!} @show
    @section('head') {!!Output::head()!!} @show
</head>
<body>
    @section('message') {!!Output::message()!!} @show
    @yield('content')
	@section('closure') {!!Output::closure()!!} @show
	@section('js') {!!Asset::js()!!} @show
</body>
</html>