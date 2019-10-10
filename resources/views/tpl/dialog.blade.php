@extends('system::tpl.default')
@section('head-css')
    @include('system::backend.tpl._style')
    <style>
        body {
            font-size : 14px;
        }
    </style>
@endsection
@section('head-script')
    @include('system::backend.tpl._script')
@endsection
@section('body-main')
    @include('system::tpl._toastr')
    <div class="container">
        @yield('dialog-main')
    </div>
@endsection