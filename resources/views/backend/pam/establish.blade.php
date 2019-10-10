@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::model($item ?? '',['route'=> [$_route, $item->id ??''], 'class' => 'layui-form']) !!}
    {!!Form::hidden('type', $type)!!}
    <div class="layui-form-item">
        {!! Form::label('username', '用户名', ['class' => 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::text('username', null, [
                'class' => 'layui-input', 'autocomplete'=> 'off',
                'readonly' => (isset($item) ? 'readonly' : false)
            ]) !!}
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('password', '密码', ['class' => 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::password('password', ['class' => 'layui-input', 'autocomplete'=> 'off']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        {!! Form::label('role_name', '用户角色', ['class' => 'layui-form-label']) !!}
        <div class="layui-input-block">
            {!! Form::select('role_name[]', $roles??[], $item_roles??[], [
                'class' => 'layui-input', 'id'=> 'roles', 'multiple', 'lay-ignore']) !!}
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block offset-3">
            {!! Form::button(isset($item) ? '编辑' :'添加', ['class'=>'layui-btn J_submit', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    <script>
	$(function() {
		$('#roles').tokenize2({
			placeholder    : '选择角色',
			tokensMaxItems : 6
		});
		$("#roles").on("tokenize:select", function() {
			$("#roles").trigger('tokenize:search', "");
		});
	})
    </script>
@endsection