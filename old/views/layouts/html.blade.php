<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('title')

    <!--[if lt IE 9]>
        <script src="/assets/js/html5shiv.min.js"></script>
        <script src="/assets/js/respond.min.js"></script>
    <![endif]-->

    @yield('header')
  </head>
  @yield('body_tag')
    @yield('body')

    @yield('closure')
  </body>
</html>