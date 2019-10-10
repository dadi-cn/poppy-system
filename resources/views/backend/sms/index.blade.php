@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        短信模板管理
        <div class="pull-right">
            <a href="{{route_url('system:backend.sms.establish')}}" class="layui-btn layui-btn-sm J_iframe">创建模板</a>
        </div>
    </div>
    <div class="layui-card-body">
        <table class="layui-table">
            <tr>
                <th class="w108">类型</th>
                <th class="w144">短信代码</th>
                <th>模板内容</th>
                <th>操作</th>
            </tr>
            @if (count($items))
                @foreach($items as $item)
                    <tr>
                        <td>{{ \Poppy\System\Models\SysConfig::kvSmsType($item['type'])}}</td>
                        <td>{{$item['code']}}</td>
                        <td>{{$item['content']}}</td>
                        <td>
                            <a class="J_iframe" title="编辑"
                               href="{{route_url('system:backend.sms.establish', [$item['id']])}}">
                                <i class="fa fa-edit text-info"></i>
                            </a>
                            <a title="删除" class="J_request"
                               data-confirm="确认删除 ?"
                               href="{{route('system:backend.sms.destroy', [$item['id']])}}">
                                <i class="fa fa-times text-danger"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6">
                        @include('system::backend.tpl.inc_empty')
                    </td>
                </tr>
            @endif
        </table>
    </div>
@endsection