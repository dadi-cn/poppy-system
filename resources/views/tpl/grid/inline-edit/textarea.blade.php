@extends('py-system::grid.inline-edit.comm')

@section('field')
	<textarea class="form-control ie-input" rows="{{ $rows }}"></textarea>
@endsection

@section('assert')
	<script>
	@component('py-system::grid.inline-edit.partials.popover', compact('trigger'))
	@slot('content')
	$template.find('textarea').text($trigger.data('value'));
	@endslot
	@slot('shown')
	$popover.find('.ie-input').focus();
	@endslot
	@endcomponent
	</script>

	{{--after submit--}}
	<script>
	@component('py-system::grid.inline-edit.partials.submit', compact('resource', 'name'))
	$popover.data('display').html(val);
	@endcomponent
	</script>
@endsection


