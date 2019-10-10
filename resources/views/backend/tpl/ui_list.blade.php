@extends('system::backend.tpl.default')
@section('backend-main')
    <div class="layui-card-header">
        {!! $title !!}
    </div>
    <div class="layui-card-body">
        @if ($search)
            {!! Form::model(input(),['method' => 'get', 'class'=> 'form-inline mt8 form-sm', 'data-pjax', 'pjax-ctr'=> '#main']) !!}
            @foreach($search as $_s)
                <div class="layui-input-inline">
                    @include('system::backend.tpl._render', [
                        'item' => $_s
                    ])
                </div>
            @endforeach
            @include('system::backend.tpl.inc_search')
            {!! Form::close() !!}
        @endif

        {{-- 页面渲染 --}}
        <table class="layui-table">
            <tr>
                @foreach($fields as $field)
                    <td>{!! $field['title'] !!}</td>
                @endforeach
                @if($handle)
                    <th>操作</th>
                @endif
            </tr>
            @if ($items->total())
                @foreach($items as $item)
                    <tr>
                        @foreach($fields as $key => $field)
                            <td>{!! data_get($item, $key) !!}</td>
                        @endforeach
                        @if ($handle)
                            <td>
                                @foreach($handle as $_h)
									<?php
									$policy = $_h['policy'] ?? '';
									$display = $policy ? $_pam->can($policy, $item) : true;
									?>
                                    @if ($display)
                                        @if ($_h['ui'] ?? '')
                                            {!! Form::ui($_h['ui'], \Poppy\System\Classes\Ui\ListUi::params($item, $_h['params']??[])) !!}
                                        @elseif ($_h['route']??'')
											<?php $con = ($_h['icon'] ?? '') ? '<i class="' . $_h['icon'] . '"></i>' : ($_h['title'] ?? 'Not Defined')  ?>
											<?php $class = $_h['class'] ?? ''  ?>
											<?php $title = $_h['title'] ?? ''  ?>
                                            <a class="{!! $class !!} {!! $title ? 'J_tooltip' : '' !!}" title="{!! $title !!}"
                                               href="{!! route($_h['route'], \Poppy\System\Classes\Ui\ListUi::params($item, $_h['params']??[])) !!}">
                                                {!! $con !!}
                                            </a>
                                        @endif
                                    @endif
                                @endforeach
                            </td>
                        @endif
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