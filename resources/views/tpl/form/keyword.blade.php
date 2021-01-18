{!! app('html')->script('/assets/libs/drag-arrange/drag-arrange.js') !!}
{!! app('html')->style('/assets/libs/drag-arrange/drag-arrange.css') !!}
<script>
layui.use('form', function() {
	$(".layui-keywords-auto-{!! $name !!}").arrangeable({
		//拖拽结束后执行回调
		callback : function(e) {
		}
	});
	$('body').on('click', ".layui-keywords-auto-{!! $name !!} .layui-icon-close", function() {
		$(this).parent().remove();
	});
});

function {!! str_replace('-', '_', $name) !!}KeywordAdd() {
	let html = '<div class="layui-input-inline layui-size-small layui-keywords-item layui-keywords-auto-{!! $name !!}">\n' +
		'{!! app('form')->text($name.'[]', '', ['class' => 'layui-input']) !!}' +
		'<i class="layui-icon layui-icon-close"></i>\n' +
		'</div>';
	$('.layui-form-auto-field-{!! $name !!}').append(html);
	setTimeout(function() {
		$(".layui-keywords-auto-{!! $name !!}").arrangeable({
			//拖拽结束后执行回调
			callback : function(e) {
			}
		});
	}, 1);
}
</script>


<div class="{{$viewClass['form-group']}} {!! (isset($errors) && !$errors->has($errorKey)) ? '' : 'has-error' !!}">

	<div class="{{$viewClass['label']}}">
		<label for="{{$id}}" class="layui-form-auto-label {{$viewClass['label_element']}}">
			@include('py-system::tpl.form.help-tip')
			{{$label}}
		</label>
	</div>

	<div class="{{$viewClass['field']}}">
		<div class="layui-form-auto-field clearfix layui-form-auto-field-{!! $name !!}">
			<?php $value = !is_null($value) ? (array) $value : [''] ?>
			@foreach($value as $v)
				<div class="layui-input-inline layui-size-small layui-keywords-item layui-keywords-auto-{!! $name !!}">
					{!! app('form')->text($name.'[]', $v, ['class' => 'layui-input']) !!}
					<i class="layui-icon layui-icon-close"></i>
				</div>
			@endforeach
		</div>
		<div class="layui-form-auto-field">
			<button type="button" class="layui-btn layui-btn-sm layui-btn-primary" onclick="{!! str_replace('-', '_', $name) !!}KeywordAdd()">添加</button>
		</div>
		@include('py-system::tpl.form.help-block')
		@include('py-system::tpl.form.error')
	</div>
</div>