@extends('poppy-system::tpl.develop')
@section('develop-main')
    @include('poppy-system::develop.inc.header')
    {!! phpinfo() !!}
@endsection