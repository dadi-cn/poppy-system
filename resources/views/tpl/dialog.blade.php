@extends('poppy-system::tpl.default')
@section('head-css')
    @include('poppy-system::backend.tpl._style')
    <style>
        body {
            font-size : 14px;
        }
    </style>
@endsection
@section('head-script')
    @include('poppy-system::backend.tpl._script')
@endsection
@section('body-main')
    @include('poppy-system::tpl._toastr')
    <div class="container">
        @yield('dialog-main')
    </div>
@endsection