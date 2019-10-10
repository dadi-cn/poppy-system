@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::open(['class'=> 'layui-form']) !!}
    <div class="layui-form-item">
        {!! Form::label('username', '用户名', ['class'=> 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::text('username', $pam->username, ['class' => 'layui-input', 'readonly' => 'readonly', 'disabled'=> 'disabled']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('password', '密码', ['class'=> 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::password('password', ['class' => 'layui-input']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('password_confirmation', '重复密码', ['class'=> 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::password('password_confirmation', ['class' => 'layui-input']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            {!! Form::button('设置密码', ['class'=>'layui-btn J_submit', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection