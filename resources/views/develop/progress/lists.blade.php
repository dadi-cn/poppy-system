@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    @if(count($all))
        <table class="layui-table">
            <tr>
                <th>模块</th>
                <th>类名</th>
                <th>是否执行</th>
                <th>操作</th>
            </tr>
            @foreach($all as $item)
                <tr>
                    <td>{{ $item['module'] }}</td>
                    <td>{{ $item['class'] }}</td>
                    <td>{{ in_array($item['class'], $already, true) ? '已执行' : '未执行' }}</td>
                    <td>
                        @if(!in_array($item['class'], $already, true))
                            <a href="{{ route_url('system:develop.progress.index', null, ['method' => $item['module'] . '.' . $item['class']]) }}"
                               data-height="800"
                               data-width="800"
                               class="J_iframe J_tooltip"
                               title="执行更新">
                                <i class="fa fa-location-arrow text-success"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection