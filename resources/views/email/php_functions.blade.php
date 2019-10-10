@extends('system::tpl.mail')
@section('mail-main')
    <h3>函数</h3>
    <table class="table-inner">
        <tr>
            <th>类型</th>
            <th>方法名称</th>
        </tr>
        @foreach($functions as $function)
            <tr>
                <td>{!! $function[0] !!}</td>
                <td>{!! $function[1] !!}</td>
            </tr>
        @endforeach
    </table>
    <h3>类方法</h3>
    <table class="table-inner">
        <tr>
            <th>类名称</th>
            <th>方法</th>
        </tr>
        @foreach($methods as $method)
            <tr>
                <td>{!! $method[0] !!}</td>
                <td>{!! $method[1] !!}</td>
            </tr>
        @endforeach
    </table>
@endsection