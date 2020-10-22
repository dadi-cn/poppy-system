@extends('poppy-system::tpl.develop')
@section('develop-main')
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(['id'=> 'form_auto']) !!}
            {!! Form::hidden('type', input('type')) !!}
            <div class="layui-form-item">
                <label for="token">Token</label>
                ( String ) [token]
                <textarea id="token" name="token" class="layui-input"></textarea>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn J_submit" type="submit" id="submit">设置token</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection