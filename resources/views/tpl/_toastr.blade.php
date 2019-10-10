@if (Session::has('end.message'))
    <script>
	setTimeout(function() {
		toastr.options = {
			closeButton   : true,
			progressBar   : true,
			showMethod    : 'fadeIn',
			timeOut       : 4000,
			positionClass : "toast-top-center",
		};
        @if (Session::get('end.level') === 0 )
		toastr.success('{!! Session::get('end.message') !!}');
        @endif
        @if (Session::get('end.level') !== 0)
		toastr.error('{!! Session::get('end.message') !!}');
        @endif
	}, 1300);
    </script>
@endif