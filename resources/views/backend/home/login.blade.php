@extends('poppy-system::tpl.default')
@section('title', $_title ?? '')
@section('description', $_description ?? '')
@section('head-css')
    @include('poppy-system::backend.tpl._style')
    <style>
        .login-pane {
            background    : rgba(1, 1, 1, 0.1);
            padding       : 20px;
            border-radius : 5px;
            margin-top    : calc((100vh - 330px) / 2);
            min-height    : 300px;
        }
        .layui-field-title legend {
            color : #fff;
        }
    </style>
@endsection
@section('head-script')
    @include('poppy-system::backend.tpl._script')
@endsection
@section('body-class', 'gray-bg backend--login')
@section('body-main')
    @include('poppy-system::tpl._toastr')
    <div class="layui-container">
        <div class="layui-col-md6 layui-col-md-offset3">
            {!! Form::open(['class'=> 'layui-form layui-form-pane login-pane']) !!}
            <fieldset class="layui-elem-field layui-field-title">
                <legend>{!! sys_setting('system::site.site_name') !!}登录</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        {!! Form::label('username', '用户名', ['class'=> 'layui-form-label']) !!}
                        <div class="layui-input-block">
                            {!! Form::text('username', null, ['class'=> 'layui-input']) !!}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        {!! Form::label('password', '密码', ['class'=> 'layui-form-label']) !!}
                        <div class="layui-input-block">
                            {!! Form::password('password', ['class'=> 'layui-input']) !!}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            {!! Form::button('登录', [
                            'class'=> 'layui-btn layui-btn-info waves-effect waves-light J_submit',
                            'type' => 'submit'
                        ]) !!}
                        </div>
                    </div>
                </div>
            </fieldset>
            {!! Form::close() !!}
            <script>
			if (top.location.href !== window.location.href) {
				top.location.href = window.location.href;
			}
			layui.form.render();
			$(function() {
				$.backstretch([
					"{!! url('assets/images/default/login/bg1.jpg')!!}",
					"{!! url('assets/images/default/login/bg2.jpg')!!}",
					"{!! url('assets/images/default/login/bg3.jpg')!!}",
					"{!! url('assets/images/default/login/bg4.jpg')!!}"
				], {fade : 1e3, duration : 8e3})
			});
            </script>
        </div>
    </div>
@endsection
