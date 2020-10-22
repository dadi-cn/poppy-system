@extends('poppy-system::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-meta')
    {!! Html::favicon('assets/images/favicon.png') !!}
@endsection
@section('head-css')
    {!! Html::style('assets/layui/css/layui.css') !!}
    {!! Html::style('assets/css/system.css') !!}
    {!! Html::style('assets/easy-web/module/admin.css') !!}
@endsection
@section('body-class', 'layui-layout-body')
@section('body-main')
    <div id="LAY_app">
        @include('poppy-system::tpl._toastr')
        <div class="layui-layout layui-layout-admin">
            @include('poppy-system::backend.tpl._header')
            @include('poppy-system::backend.tpl._sidemenu')
            @include('poppy-system::backend.tpl._pagetabs')
            <!-- 主体内容 -->
            <div class="layui-body" id="LAY_app_body">
                <div class="layadmin-tabsbody-item layui-show">
                    <iframe src="{!! route('system:backend.home.cp') !!}" frameborder="0" class="layadmin-iframe" style="background: #fff;"></iframe>
                </div>
            </div>
        </div>
        {!! Html::script('assets/js/system_vendor.js') !!}
        {!! Html::script('assets/js/system_cp.js') !!}
        {!! Html::script('assets/layui/layui.js') !!}
        <script>
		layui.config({
			base : "/assets/easy-web/module/"
		}).use(['layer', 'admin', 'index'], function() {
			var $ = layui.jquery;
			var layer = layui.layer;
			var admin = layui.admin;
			var index = layui.index;

			// 移除loading动画
			setTimeout(function() {
				admin.removeLoading();
			}, window == top ? 600 : 100);

			// 默认加载主页
			index.loadHome({
				menuPath: '{!! route('system:backend.home.cp') !!}',
				menuName: '<i class="layui-icon layui-icon-home"></i>'
			});

		});
        </script>
    </div>
    <!-- 加载动画，移除位置在common.js中 -->
    <div class="page-loading">
        <div class="ring-loading"><div></div><div></div><div></div><div></div></div>
    </div>
@endsection