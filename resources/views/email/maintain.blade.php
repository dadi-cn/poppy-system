@extends('system::tpl.mail')
@section('mail-main')
    <h3>{!! $title ?? '' !!}</h3>
    @include('system::tpl.mail.article_start')
    {!! $content ?? '' !!}
    @include('system::tpl.mail.article_end')
@endsection