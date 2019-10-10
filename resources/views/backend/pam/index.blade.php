@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        用户管理
        <div class="pull-right">
            @can('create', System\Models\PamRole::class)
                <a href="{{route_url('system:backend.pam.establish', [], ['type' => $type,])}}"
                   class="layui-btn layui-btn-sm J_iframe">创建用户</a>
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
        {!! Form::model(input(),['method' => 'get', 'class'=> 'form-inline mt8 form-sm', 'data-pjax', 'pjax-ctr'=> '#main']) !!}
        {!!Form::hidden('type', $type)!!}
        <div class="layui-input-inline">
            {!! Form::text('passport', null, ['placeholder' => '通行证', 'class' => 'layui-input']) !!}
        </div>
        <div class="layui-input-inline">
            {!! Form::select('role_id', $roles, null, ['placeholder'=> '用户角色', 'class' => 'layui-input']) !!}
        </div>
        @include('system::backend.tpl.inc_search')
        {!! Form::close() !!}

        <table class="layui-table">
            <tr>
                <th>用户ID</th>
                <th>用户名</th>
                <th>手机</th>
                <th>邮箱</th>
                <th>登录次数</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            @if ($items->total())
                @foreach($items as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->username}}</td>
                        <td>{{$item->mobile}}</td>
                        <td>{{$item->email}}</td>
                        <td>{{$item->login_times}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>
                            @if ($_pam->can('disable', $item))
                                <a class="J_iframe J_tooltip" title="当前启用, 点击禁用"
                                   href="{{route_url('system:backend.pam.disable',[$item->id])}}">
                                    <i class="fa fa-unlock text-success"></i>
                                </a>
                            @endif
                            @if ($_pam->can('enable', $item))
                                <a class="J_request J_tooltip" title="当前禁用, 点击启用"
                                   href="{{route_url('system:backend.pam.enable',[$item->id])}}">
                                    <i class="fa fa-lock text-danger"></i>
                                </a>
                            @endif
                            <a title="编辑[{{$item->username}}]" class="J_iframe J_tooltip"
                               href="{{route('system:backend.pam.establish', [$item->id])}}">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a title="修改密码" class="J_iframe J_tooltip"
                               href="{{route('system:backend.pam.password', [$item->id])}}">
                                <i class="fa fa-key"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">
                        @include('system::backend.tpl.inc_empty')
                    </td>
                </tr>
            @endif
        </table>
        {!! $items->render() !!}
    </div>
@endsection
