@extends('poppy-system::backend.tpl.dialog')
@section('backend-main')
    @if (isset($item))
        {!! Form::model($item,['route' => [$_route, $item->id],'class' => 'layui-form']) !!}
    @else
        {!! Form::open(['class' => 'layui-form']) !!}
    @endif
    <div class="layui-form-item">
        {!! Form::label('title', '角色名称', ['class' => 'layui-form-label strong validation']) !!}
        <div class="layui-input-block">
            {!! Form::text('title', null,['class'=>'layui-input']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('name', '角色标识', ['class' => 'layui-form-label strong validation']) !!}
        <div class="layui-input-block">
            @if (isset($item))
                {!! Form::text('name', null,[
                    'class'    => 'layui-input',
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ]) !!}
            @else
                {!! Form::text('name', null,['class'=>'layui-input']) !!}
            @endif
            <small class="layui-word-aux">
                选定保存后不可编辑
            </small>
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('guard', '角色组', ['class' => 'layui-form-label strong validation']) !!}
        <div class="layui-input-block">
            @if (isset($item))
                {!!Form::select('guard', \Poppy\System\Models\PamAccount::kvType(),  $item->type,[
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ])!!}
            @else
                {!!Form::select('guard', \Poppy\System\Models\PamAccount::kvType(), Request::input('type'),[
                    'class'=>'layui-input'])!!}
            @endif
            <small class="layui-word-aux">
                选定保存后不可编辑
            </small>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'layui-btn J_submit', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection