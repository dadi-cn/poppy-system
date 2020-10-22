@extends('poppy-system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::model($item ?? null, ['route' => [$_route, $item['id'] ?? ''], 'class' => 'layui-form']) !!}
    <div class="layui-form-item">
        {!! Form::label('type', '类型:', ['class' => 'validation']) !!}
        {!! Form::select('type', \Poppy\System\Models\SysConfig::kvSmsType(), null, ['placeholder'=>'选择类型']) !!}
    </div>
    <div class="layui-form-item">
        {!! Form::label('code', 'SmsCode:', ['class' => 'validation']) !!}
        {!! Form::text('code', null, ['class' => 'layui-input']) !!}
    </div>
    <div class="layui-form-item">
        {!! Form::label('content', '内容(本地测试):', ['class' => 'validation']) !!}
        {!! Form::tip('验证码可用变量名称[code:验证码], 遵循 laravel translate 写法, 会显示在日志中') !!}
        {!! Form::textarea('content', null, ['class' => 'layui-textarea', 'rows' => 3]) !!}
    </div>
    {!! Form::button(isset($item) ? '编辑' : '添加', ['class'=>'layui-btn J_submit', 'type'=> 'submit']) !!}
    {!! Form::close() !!}
@endsection