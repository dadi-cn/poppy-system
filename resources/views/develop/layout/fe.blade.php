@extends('poppy-system::backend.tpl.fe')
@section('backend-fe')
    <div class="layui-container">
        <ul class="layui-nav layui-bg-cyan">
            <li class="layui-nav-item">
                <a class="fa fa-home" href="{!! route('system:develop.cp.cp') !!}"></a>
            </li>
            <li class="layui-nav-item {!! active_class(if_query('type', ''), 'layui-this') !!}">
                <a href="?type=">开发组件</a>
            </li>
            <li class="layui-nav-item {!! active_class(if_query('type', 'form'), 'layui-this') !!}">
                <a href="?type=form">表单示例</a>
            </li>
        </ul>
        @if(if_query('type', ''))
            @include('poppy-system::develop.layout.fe-index')
        @endif
        @if(if_query('type', 'upload'))
            @include('poppy-system::develop.layout.fe-upload')
        @endif
        @if(if_query('type', 'form'))
            @include('poppy-system::develop.layout.fe-form')
        @endif
    </div>
@endsection