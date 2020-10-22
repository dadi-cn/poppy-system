@extends('poppy-system::tpl.default')
@section('title')
    {!! $_title ?? '' !!}
@endsection
@section('head-meta')
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Cache-Control" content="no-transform"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <meta name="renderer" content="webkit"/>
@endsection
@section('head-css')
    {!! Html::style(mix('assets/css/mobile.css')) !!}
    <style>
        html {
            -webkit-text-size-adjust : none;
        }
        @for($fontSize=10;$fontSize<=20.1;$fontSize+=0.1)
        @media screen and (min-width : {!! (640*$fontSize)/20 !!}px) {
            html {
                font-size : {!! $fontSize !!}px !important;
            }
        }
        @endfor
    </style>
    {{--
    - 背景全适配解决方案, 页面大小为 320-640 不得更宽
    - 使用 rem 自动计算解决
    - 使用 0.1 像素差去计算
    - 启用浏览器最小字体限制
    --}}
@endsection
@section('head-script')
    {!! Html::script(mix('assets/js/mobile.js'),['merge'=> 1]) !!}
@endsection