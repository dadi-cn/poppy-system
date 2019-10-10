@extends('system::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-css')
    @include('system::backend.tpl._style')
@endsection
@section('head-script')
    @include('system::backend.tpl._script')
@endsection
@section('body-class', 'develop')
@section('body-main')
    @include('system::tpl._toastr')
    <div class="layui-container">
        @yield('develop-main')
    </div>
@endsection