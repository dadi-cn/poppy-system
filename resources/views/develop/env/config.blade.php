@extends('poppy-system::tpl.develop')
@section('develop-main')
    @include('poppy-system::develop.inc.header')
    <div class="layui-tab layui-tab-card">
        <ul class="layui-tab-title">
            @foreach($pages as $key => $conf)
                <li class="{!! active_class($path === $key, 'layui-this') !!}">
                    <a href="{!! route('system:develop.env.config', [$key]) !!}">
                        {!! $conf['title'] !!}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="layui-tab-content">
			<?php $i = 0 ?>
            @foreach($tabs as $group_key => $group)
                <h4>{!! $group['title'] ?? '其他'  !!}</h4>
                <table class="layui-table">
                    <tr>
                        <td class="w276">Key</td>
                        <td class="w144">Label</td>
                        <td class="w120">Value</td>
                        <td>Description</td>
                    </tr>
                    @foreach($group['fields'] as $item_key => $item)
                        <tr class="{!! active_class(in_array('required', $item['validates_result'], false) || is_null($item['value']), 'layui-bg-orange') !!}">
                            <td>{!! $item['key'] !!}</td>
                            <td>{!! $item['label'] !!}</td>
                            <td>
                                @if (is_null($item['value']))
                                    {!! '/-/' !!}
                                @elseif (is_string($item['value']))
                                    {!! str_limit($item['value'], 10) !!}
                                @elseif (is_bool($item['value']))
                                    {!! (int) $item['value'] !!}
                                @endif
                            </td>
                            <td>
                                {!! active_class(in_array('required', $item['validates_result'], false), '(必填)') !!}
                                {!! $item['placeholder']??'' !!}{!! $item['description']??'' !!}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
    </div>
    <script>
	layui.form.render();
    </script>
@endsection