@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::model($item ?? null, ['id'=> 'reason', 'class'=> 'layui-form']) !!}
    <div class="layui-form-item">
        {!! Form::label('to', '邮箱', ['class'=> 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::text('to', null, ['class'=> 'layui-input']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('content', '内容', ['class'=> 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::text('content', null, ['class'=> 'layui-input']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            {!! Form::button('发送',['type' => 'submit', 'class'=> 'layui-btn J_submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection
