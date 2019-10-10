@extends('system::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-css')
    @include('system::backend.tpl._style')
@endsection
@section('head-script')
    @include('system::backend.tpl._script')
@endsection
@section('body-main')
    @include('system::tpl._toastr')
    <main class="backend--main pd10" style="background: #fff">
        @yield('backend-main')
    </main>
    <script>
	layui.form.render();
    </script>
@endsection