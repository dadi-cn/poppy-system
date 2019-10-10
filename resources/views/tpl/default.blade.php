<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="keywords" content="@yield('keywords')">
    <meta name="description" content="@yield('description')">
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    @yield('head-meta')
    @yield('head-css')
    @yield('head-script')
</head>
<body class="@yield('body-class')" style="@yield('body-style')">

@yield('body-main')

</body>

@yield('footer-script')

</html>
