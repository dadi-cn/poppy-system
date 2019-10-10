@extends('system::tpl.default')
@section('title', $_title ?? sys_setting('system::site.title'))
@section('keywords', $_keyword ?? sys_setting('system::site.keywords'))
@section('description', $_description ?? sys_setting('system::site.description'))
@section('head-css')
    {!! Html::style(mix('assets/css/web.css')) !!}
@endsection
@section('head-script')
    {!! Html::script(mix('assets/js/web.js')) !!}
@endsection