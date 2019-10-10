@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        邮件配置
    </div>
    <div class="layui-card-body">
        {!! Form::model($item, ['route' => 'system:backend.mail.store', 'class'=> 'layui-form']) !!}
        <div class="layui-form-item">
            {!! Form::label('driver', '发送方式', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::radios('driver', [
                    'mail' => '内置Mail函数',
                    'smtp' => 'SMTP 服务器',
                ]) !!}
            </div>
        </div>

        <div class="layui-form-item">
            {!! Form::label('encryption', '加密方式', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::radios('encryption', [
                    'none' => '不加密',
                    'ssl' => 'SSL',
                ]) !!}
            </div>
        </div>

        <div class="layui-form-item">
            {!! Form::label('port', '服务器端口', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::text('port', null, ['class'=> 'layui-input']) !!}
            </div>
        </div>
        <div class="layui-form-item">
            {!! Form::label('host', '服务器地址', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::text('host', null, ['class'=> 'layui-input']) !!}
            </div>
        </div>

        <div class="layui-form-item">
            {!! Form::label('from', '发邮箱地址', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::text('from', null, ['class'=> 'layui-input']) !!}
            </div>
        </div>

        <div class="layui-form-item">
            {!! Form::label('username', '账号', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::text('username', null, ['class'=> 'layui-input']) !!}
            </div>
        </div>

        <div class="layui-form-item">
            {!! Form::label('password', '密码', ['class'=> 'layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::text('password', null, ['class'=> 'layui-input']) !!}
                <div class="layui-form-item">
                </div>
            </div>
            <div class="layui-input-block offset-2">
                {!! Form::button('修改配置',['type' => 'submit', 'class'=> 'layui-btn J_submit']) !!}
                <a href="{!! route_url('system:backend.mail.test') !!}" class="J_iframe layui-btn layui-btn-primary">
                    发送测试邮件
                </a>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
@endsection
