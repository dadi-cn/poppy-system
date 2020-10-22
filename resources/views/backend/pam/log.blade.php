@extends('poppy-system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        账户日志
    </div>
    <div class="layui-card-body">
        {!! Form::model(input(), ['method' => 'get', 'class' => 'layui-form', 'data-pjax', 'pjax-ctr'=>'#main']) !!}
        <div class="layui-input-inline">
            {!! Form::text('account', null, ['class' => 'layui-input', 'placeholder' => '用户ID']) !!}
        </div>
        <div class="layui-input-inline">
            {!! Form::text('ip', null, ['class' => 'layui-input', 'placeholder' => 'IP地址']) !!}
        </div>
        <div class="layui-input-inline">
            {!! Form::text('area', null, ['class' => 'layui-input', 'placeholder' => '登陆地区']) !!}
        </div>
        @include('poppy-system::backend.tpl.inc_search')
        {!! Form::close() !!}
        <table class="layui-table">
            <tr>
                <th class="w72">ID</th>
                <th class="w108">用户名</th>
                <th class="w144">操作时间</th>
                <th class="w144">IP地址</th>
                <th class="w108">成功/失败</th>
                <th>说明</th>
            </tr>
            @if ($items->total())
                @foreach($items as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->pam ? $item->pam->username : '-'}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->ip}}</td>
                        <td>{{$item->type}}</td>
                        <td>{{$item->area_text}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">暂无数据</td>
                </tr>
            @endif
        </table>
        {!! $items->render() !!}
    </div>
@endsection