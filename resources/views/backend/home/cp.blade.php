@extends('poppy-system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        主页
    </div>
    <div class="layui-card-body">
        <div class="layui-row layui-col-space10">
            {!! Form::open(['data-pjax', 'pjax-ctr'=> '#main']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection