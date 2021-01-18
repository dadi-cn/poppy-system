{!! app('html')->script('assets/libs/boot/simditor.min.js') !!}
{!! app('html')->style('assets/libs/boot/simditor.css') !!}
<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-system::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field">
			{!! app('poppy.mgr-page.form')->editor($name, old($column, $value), [
				'placeholder' => $placeholder
			]) !!}
		</div>
		@include('py-system::tpl.form.help-block')
		@include('py-system::tpl.form.error')
	</div>
</div>
