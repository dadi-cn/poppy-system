@extends('poppy-system::tpl.develop')
@section('develop-main')
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(['class'=> 'layui-form']) !!}
            <div class="layui-form-item">
                <label for="token">{!! $field !!}</label>
                ( String ) [{!! $field !!}]
                <input id="token" name="token" class="layui-input"/>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn J_submit" type="submit" id="submit">设置{!! $field !!}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection