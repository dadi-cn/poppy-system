@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        角色管理
        <div class="pull-right">
            @can('create', \Poppy\System\Models\PamRole::class)
                <a href="{{route_url('system:backend.role.establish', [], ['type' => $type,])}}"
                   class="layui-btn layui-btn-sm J_iframe">
                    创建角色
                </a>
            @endcan
        </div>
    </div>
    <div class="layui-card-body">
        <div class="layui-tab">
            <ul class="layui-tab-title">
				<?php $i = 0 ?>
                @foreach($types as $type_key => $type_title)
                    <li class="{!! $type === $type_key ? 'layui-this' : '' !!}">
                        <a href="{!! route_url('', [], ['type'=> $type_key]) !!}">{!! $type_title !!}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <table class="layui-table">
            <tr>
                <th>ID</th>
                <th>角色</th>
                <th>角色显示名称</th>
                <th>操作</th>
            </tr>
            @foreach($items as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->title}}</td>
                    <td>
                        @can('permission', $item)
                            <a class="fa fa-user-check J_iframe"
                               data-title="编辑 [{{$item->title}}] 权限"
                               data-width="600"
                               href="{{route('system:backend.role.menu', [$item->id])}}"></a>
                        @endcan
                        @can('edit', $item)
                            <a class="J_iframe" href="{{route('system:backend.role.establish', [$item->id])}}">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endcan
                        @can('delete', $item)
                            <a class="text-danger J_request J_tooltip" title="删除角色"
                               data-confirm="确认删除角色 `{!! $item->title !!}`?"
                               href="{{route('system:backend.role.delete', [$item->id])}}">
                                <i class="fa fa-times"></i>
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="layui-card-pager">
            {!! $items->appends(input())->render() !!}
        </div>
    </div>
@endsection