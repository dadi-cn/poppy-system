@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        修改密码
    </div>
    <div class="layui-card-body">
        {!! Form::open(['class'=> 'layui-form']) !!}
        <div class="layui-form-item">
            {!! Form::label('old_password', '老密码', ['class'=>'validation layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::password('old_password',['class' => 'layui-input', 'data-rule-required'=> 'true']) !!}
            </div>
        </div>
        <div class="layui-form-item">
            {!! Form::label('password', '密码', ['class'=>'validation layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::password('password',['class' => 'layui-input', 'data-rule-required'=> 'true', 'id'=> 'password']) !!}
            </div>
        </div>
        <div class="layui-form-item">
            {!! Form::label('password_confirmation', '重复密码', ['class'=>'validation layui-form-label']) !!}
            <div class="layui-input-block">
                {!! Form::password('password_confirmation',[
                    'class' => 'layui-input',
                    'data-rule-required'=> 'true', 'data-rule-equalto'=>'#password'
                ]) !!}
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                {!! Form::button('修改密码', ['class'=>'layui-btn J_validate', 'type'=> 'submit']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection