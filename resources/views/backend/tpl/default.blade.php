@extends('system::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@if (!sys_is_pjax())
@section('head-meta')
    {!! Html::favicon('assets/images/favicon.png') !!}
@endsection
@section('head-css')
    @include('system::backend.tpl._style')
@endsection
@section('head-script')
    @include('system::backend.tpl._script')
@endsection
@endif
@section('body-main')
    @include('system::tpl._toastr')
    <div class="layui-fluid system--page" data-pjax pjax-ctr="#main" id="main">
        <div class="layui-card">
            @yield('backend-main')
        </div>
    </div>
    <script>
	layui.use(['form'], function() {
		layui.form.render();
	})
    </script>
@endsection