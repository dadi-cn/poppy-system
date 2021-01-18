<div class="input-group input-group-sm">
	@if($group)
		<div class="input-group-btn">
			<input type="hidden" name="{{ $id }}_group" class="{{ $group_name }}-operation" value="0"/>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="min-width: 32px;">
				<span class="{{ $group_name }}-label">{{ $default['label'] }}</span>
				&nbsp;&nbsp;
				<span class="fa fa-caret-down"></span>
			</button>
			<ul class="dropdown-menu {{ $group_name }}">
				@foreach($group as $index => $item)
					<li><a href="#" data-index="{{ $index }}"> {{ $item['label'] }} </a></li>
				@endforeach
			</ul>
		</div>
	@endif
	<div class="layui-input-group">
		<span class="layui-input-group-addon"><i class="fa fa-calendar"></i></span>
		{!! app('form')->text($name, request($name, $value),  [
			'class' => 'layui-input',
			'id' => $id,
			'placeholder' => $label,
		]) !!}
		<script>
		layui.laydate.render($.extend({
			elem : '#{!! $id !!}'
		}, {!! json_encode($options) !!}));
		</script>
	</div>
</div>