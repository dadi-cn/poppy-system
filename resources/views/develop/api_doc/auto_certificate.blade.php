@if (data_get($definition, 'sign_certificate'))
    <div class="alert alert-warning">
        @foreach(data_get($definition, 'sign_certificate') as $field)
            <div class="layui-form-item">
                {!! ($field['is_required'] == 'N' ? '' : '<span style="color:red">*</span>') !!}
                {!! Form::label($field['name'], strip_tags($field['title'])) !!}
                (
                {!! strip_tags($field['type']) !!}
                [{!! $field['name'] !!}]
                )
                <a href="{!! route_url('system:develop.doc.field', [$guard,$field['name']]) !!}"
                   data-title="设置 {!! $field['title'] !!}"
                   class="J_iframe pull-right">设置 {!! $field['title'] !!}</a>
                {!! Form::text($field['name'], Session::get('dev_token#' . $guard . '#' . $field['name']), [
                    'class' => 'layui-input layui-input-sm J_calc', 'readonly'=> 'readonly']) !!}
            </div>
        @endforeach
        @if (data_get($definition, 'sign_generate', false))
            <div class="layui-form-item">
                {!! Form::label('sign', '当前 sign:') !!}
                {!! Form::text('sign',$data['token']??'', [
                    'class' => 'layui-input layui-input-sm mt3', 'id' => 'sign',
                    'readonly'=> true]) !!}
            </div>
            <script>
			function calc_sign() {
                {!! data_get($definition, 'sign_generate', false) !!}
			}

			$(function() {
				calc_sign();
				$('.J_calc').on('input propertychange', function() {
					calc_sign();
				});
			});
            </script>
        @endif
    </div>
@endif