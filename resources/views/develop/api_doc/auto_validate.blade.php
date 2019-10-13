'rules'       : {
    @if (isset($data['current_params']) && $data['current_params'])
        @foreach($data['current_params'] ?? [] as $param)
            @if (!\Illuminate\Support\Str::startsWith($param->field, ':'))
                {!! $param->field !!} : {
                required : {!! ($param->optional ? 'false' : 'true') !!}
                },
            @endif
        @endforeach
    @endif
    _pre:{required:false}
},