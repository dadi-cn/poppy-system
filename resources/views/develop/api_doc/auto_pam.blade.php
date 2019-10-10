@if (data_get($pam??[], 'id'))
    {!! Form::open(['class'=> 'layui-form mt10']) !!}
    <div class="layui-input-inline">
        {!! Form::label('id', 'id : '.$pam['id'], ['class'=> 'layui-form-label text-left']) !!}
    </div>
    <div class="layui-input-inline">
        {!! Form::label('mobile', 'mobile : '.$pam['mobile'], ['class'=> 'layui-form-label text-left']) !!}
    </div>
    <div class="layui-input-inline">
        {!! Form::label('username', 'ua : '.$pam['username'], ['class'=> 'layui-form-label text-left']) !!}
    </div>
    {!! Form::close() !!}
    <hr>
@endif