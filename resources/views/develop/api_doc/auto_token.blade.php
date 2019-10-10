@if (data_get($definition, 'sign_token', true))
    <p class="layui-form-item mt10">
        {!! Form::label('token', '当前 Token:') !!}
        @if (Route::has('system:develop.cp.set_token'))
            <a href="{!! route_url('system:develop.cp.set_token', null, ['type'=> $guard]) !!}"
               data-title="设置 Token {!! $guard !!}"
               class="J_iframe pull-right layui-btn layui-btn-sm">设置 Token</a>
        @endif
        @if (Route::has('system:develop.cp.api_login'))
            <a href="{!! route_url('system:develop.cp.api_login', null, ['type'=> $guard]) !!}"
               data-title="登录 {!! $guard !!}"
               class="J_iframe pull-right layui-btn layui-btn-sm mr10">登录</a>
        @endif
        {!! Form::text('token',$data['token']??'', [
            'class' => 'layui-input J_calc mt3 layui-input-sm',
            'readonly'=> true,
        ]) !!}
    </p>
@endif