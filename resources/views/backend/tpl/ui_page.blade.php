@extends('poppy-system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        {!! $title !!}
    </div>
    <div class="layui-card-body">
        {!! Form::model(input(), ['url' => $url, 'class'=> 'layui-form']) !!}
        @foreach($fields as $item_key => $item)
            <div class="layui-form-item">
                <label for="" class="layui-form-label
                    {!! (isset($item['validates']['required']) &&  $item['validates']['required']) ? 'validation' :'' !!}">
                    {!! $item['label'] !!}
                </label>
                <div class="layui-input-block">
                    @include('poppy-system::backend.tpl._render', [
                       'item' => $item
                    ])
                </div>
            </div>
        @endforeach
        <div class="layui-form-item">
            <div class="layui-input-block">
                {!! Form::button('提交',['class'=>'layui-btn J_submit', 'type'=>'submit']) !!}
            </div>
        </div>
        {!!Form::close()!!}
    </div>
    <script>
	layui.form.render();
    </script>
@endsection