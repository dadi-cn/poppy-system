<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-system::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">

		<input type="file" class="{{$class}}" name="{{$name}}[]" {!! $attributes !!} />
		@isset($sortable)
			<input type="hidden" class="{{$class}}_sort" name="{{ $sort_flag."[$name]" }}"/>
		@endisset
		@include('py-system::tpl.form.help-block')
		@include('py-system::tpl.form.error')
	</div>
</div>
