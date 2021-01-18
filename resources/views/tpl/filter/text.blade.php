@if($group)
	{!! app('form')->select($id .'_group', $group, null, [
		'lay-filter' => $id.'-lay-filter'
	]) !!}
	<script>
    layui.form.render('select', '{!! $id !!}-lay-filter');
	</script>
@endif
{!! app('form')->input($type, $name,  request($name, $value), [
    'class' => 'J_tooltip layui-input',
    'title' => $label,
    'placeholder' => $placeholder,
] ) !!}