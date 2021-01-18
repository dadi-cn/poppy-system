<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">
	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-system::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}} layui-form-color-label">
		<div class="layui-form-auto-field">
			<div class="layui-inline">
				{!! app('form')->text($name, $value, $attributes + [
					'readonly'
				]) !!}
			</div>
			<div class="layui-inline">
				<div id="{!! $attributes['id'] !!}-selector"></div>
			</div>
			<script>
			layui.colorpicker.render({
				elem : '#{!! $attributes['id'] !!}-selector',
				color: '{!! $value !!}',
				done : function(color) {
					$('#{!! $attributes['id'] !!}').val(color);
				}
			});
			</script>
		</div>
		@include('py-system::tpl.form.help-block')
		@include('py-system::tpl.form.error')
	</div>
</div>