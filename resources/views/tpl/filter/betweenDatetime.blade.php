<div class="layui-input-group layui-input-inline">
    <span class="layui-input-group-addon">
        {{$label}}
    </span>
    <div class="layui-row">
        <div class="layui-col-md5">
            {!! Form::datePicker($name['start'], request()->input("{$column}.start", \Illuminate\Support\Arr::get($value, 'start')), [
                'placeholder' => $label
            ]) !!}
        </div>
        <div class="layui-col-md5 layui-col-md-offset2">
            {!! Form::datePicker($name['end'], request()->input("{$column}.end", \Illuminate\Support\Arr::get($value, 'end')), [
                'placeholder' => $label
            ]) !!}
        </div>
    </div>
</div>