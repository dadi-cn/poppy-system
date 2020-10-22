@extends('poppy-system::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-css')
    @include('poppy-system::backend.tpl._style')
@endsection
@section('head-script')
    @include('poppy-system::backend.tpl._script')
@endsection
@section('body-class', 'mdb-skin-custom fixed-sn')
@section('body-main')
    @yield('backend-fe')
@endsection