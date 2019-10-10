@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    {!! phpinfo() !!}
@endsection