@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">扩展管理</div>
    <div class="layui-card-body">
        <div class="layui-row layui-col-space10">
            @if (count($items))
                @foreach($items as $item)
                    <div class="layui-col-md3">
                        <div class="layui-card">
                            <div class="layui-card-header">{!! $item['title'] !!}</div>
                            <div class="layui-card-body">
                                {!! $item['description'] !!}
                            </div>
                            <div class="layui-card-footer">
                                <a href="{!! route_url('system:backend.addon.config', [$item['folder']]) !!}" class="J_iframe">
                                    <i class="fa fa-cog"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @include('system::backend.tpl.inc_empty')
            @endif
        </div>
    </div>
@endsection