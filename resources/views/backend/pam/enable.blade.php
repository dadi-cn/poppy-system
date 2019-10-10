@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::model($user ?? null, ['route'=> [$_route, $id]]) !!}
    {!! Form::hidden('id', $id ?? 0) !!}
    <div class="layui-form-item">
        {!! Form::label('date', '解禁日期') !!}
        {!! Form::datePicker('date', $user->disable_end_at, ['class' => 'layui-input', 'disabled' => true]) !!}
    </div>
    <div class="layui-form-item">
        {!! Form::label('reason', '原因') !!}
        {!! Form::textarea('reason',null, ['class' => 'layui-textarea', 'rows'=> 3]) !!}
    </div>
    <div class="layui-form-item">
        {!! Form::multiThumb('pictures', null, ['pam' => $_pam, 'number' => 9]) !!}
    </div>
    <div class="layui-form-item">
        {!! Form::button('解禁', ['class'=>'layui-btn J_submit', 'type'=> 'submit']) !!}
    </div>
    {!! Form::close() !!}
@endsection