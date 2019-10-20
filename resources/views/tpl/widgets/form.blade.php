<form {!! $attributes !!}>
    <div class="box-body fields-group">

        @foreach($fields as $field)
            {!! $field->render() !!}
        @endforeach

    </div>

    @if ($method !== 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif

    @if(count($buttons) > 0)
        <div class="layui-row">
            <div class="layui-col-md{{$width['label']}}"></div>
            <div class="layui-col-md{{$width['field']}}">
                @if(in_array('reset', $buttons, true))
                    <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">{{ trans('admin.reset') }}</button>
                @endif

                @if(in_array('submit', $buttons, true))
                    <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm">{{ trans('admin.submit') }}</button>
                @endif
            </div>
        </div>
    @endif
</form>
