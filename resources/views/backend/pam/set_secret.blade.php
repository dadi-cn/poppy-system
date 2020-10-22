@extends('poppy-system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::open(['route' => [$_route, $id]]) !!}
        {!! Form::hidden('id', $id) !!}
        <div class="layui-form-item">
            {!! Form::label('app_secret', '加密秘钥') !!}
            {!! Form::text('app_secret', null, ['class' => 'layui-input']) !!}
        </div>
        <div class="layui-form-item">
            {!! Form::button('设置', ['class' => 'layui-btn J_submit', 'type' => 'submit']) !!}
        </div>
    {!! Form::close() !!}
@endsection