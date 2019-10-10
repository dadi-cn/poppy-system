@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        前端帮助
    </div>
    <div class="layui-card-body">
        <div>
            Pjax Content #main {!! str_random(5) !!}
        </div>
        {!! Form::open(['class'=> 'layui-form', 'data-pjax', 'pjax-ctr'=> '#main']) !!}
        <div class="layui-form-item">
            <div class="layui-inline">
                <button class="layui-btn layui-btn-sm">Pjax 请求刷新 #main</button>
            </div>
        </div>
        {!! Form::close() !!}
        {!! Form::open(['class'=> 'layui-form', 'data-pjax']) !!}
        <div class="layui-form-item">
            <div class="layui-inline">
                <button class="layui-btn layui-btn-sm">Pjax 请求刷新 #pjax-container</button>
            </div>
        </div>
        {!! Form::close() !!}
        <div id="pjax-container">
            Pjax Content #pjax-container {!! str_random(5) !!}
        </div>

        <table class="layui-table">
            <tr>
                <th>操作</th>
                <th>说明</th>
            </tr>
            <tr>
                <td>刷新</td>
                <td>
                    <a href="#" class="J_reload layui-btn layui-btn-sm">刷新(Reload)</a>
                    <a href="{!! route_url('', ['popup']) !!}" class="J_iframe layui-btn layui-btn-sm">弹出(Popup)</a>
                </td>
            </tr>
            <tr>
                <td>打开</td>
                <td>
                    <a href="http://www.baidu.com" class="layui-btn layui-btn-sm">打开百度</a>
                </td>
            </tr>
        </table>
    </div>
@endsection