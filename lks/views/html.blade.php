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
<body class="@section('bodyclass'){{Output::bodyclass()}}@show">
    <div id="message" class="container region">
        @section('message') {!!Output::message()!!} @show
    </div>

    <div id="header" class="container region">
        @section('header') @include('block_header') @show
    </div>

    <div id="primary-menu" class="container region">
        @section('primary-menu') @show
    </div>

    <div id="breadcrumb" class="container region">
        @section('breadcrumb') {!!Output::breadcrumb()!!} @show
    </div>

    <div id="content" class="container region">
        <div id="left_sidebar" class="sidebar">
            @section('sidebar-left') @show
        </div>

        <div id="main_sidebar"><div id="squeeze"><div id="main_sidebar_content">
            @yield('content')
        </div></div></div>

        <div id="right_sidebar" class="sidebar">
            @section('sidebar-right') @show
        </div>
    </div>

    <div id="footer" class="container region">
        @section('footer') @show
    </div>

    @section('closure') {!!Output::closure()!!} @show
    @section('js') {!!Asset::js()!!} @show
</body>
</html>