@extends('system::backend.tpl.dialog')
@section('backend-main')
    {{--  要想渲染页面, 必须要有 layui-form 类--}}
    {!! Form::open(['route' => [$_route, $role->id], 'class'=> 'layui-form']) !!}
    <div class="layui-tab layui-tab-brief">
        <ul class="layui-tab-title">
			<?php $display = 0;?>
            @foreach($permission as $pk => $pv)
                <li class="<?php echo $display++ === 0 ? 'layui-this' : ''; ?>">{!! $pv['title'] !!}</li>
            @endforeach
        </ul>
        <div class="layui-tab-content">
			<?php $display = 0;?>
            @foreach($permission as $pk => $pv)
                <div class="layui-tab-item {!! $display++ === 0 ? 'layui-show' : '' !!}" id="{!! $pk !!}">
                    @if ($pv['groups'])
                        <table class="layui-table">
                            <tr>
                                <th class="w108">分组</th>
                                <th>权限</th>
                            </tr>
                            @foreach($pv['groups'] as $gk => $gv)
                                <tr>
                                    <td>{!! $gv['title'] !!}</td>
                                    <td>
                                        @foreach($gv['permissions'] as $sk => $sv)
                                            {!! Form::checkbox('permission_id[]', $sv['id'], $sv['value'], [
                                                'title'=> $sv['description'],
                                                'lay-skin'=>'primary'
                                            ]) !!}
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    {!! Form::button('保存', ['class'=>'layui-btn J_submit', 'type'=>'submit']) !!}
    {!!Form::close()!!}
    <script>
	$(function() {
		layui.form.render();
	})
    </script>
@endsection