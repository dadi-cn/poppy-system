@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    <div style="display: flex">
        <div style="flex: 0 0 75%;">
            <div class="dev--box pd8">
                @foreach($items as $_slug)
                    <blockquote class="layui-elem-quote">
                        {!! $_slug['slug'] !!} ( {!! $_slug['description'] !!} )
                    </blockquote>
                    @foreach($_slug['tables'] as $_table)
                        <caption>
                            <a name="{!! $_table['table'] !!}">
                                {!! $_table['table'] !!}( {!! $_table['description'] !!} )
                            </a>
                        </caption>
                        <table class="layui-table">
                            <tr>
                                <th class="w240">字段</th>
                                <th class="w144">类型</th>
                                <th>注释</th>
                            </tr>
                            @foreach($_table['fields'] as $_field)
                                <tr>
                                    <td>{!! $_field['variable'] !!}</td>
                                    <td>{!! $_field['type'] !!}</td>
                                    <td>{!! $_field['description']!!}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                @endforeach
            </div>
        </div>
        <div class="dev--box" style="flex: 0 0 25%;">
            <ul class="layui-nav layui-nav-tree" id="tree" lay-filter="tree" style="width: auto;position: sticky;top:5px;">
                @foreach($items as $_slug)
                    <li class="layui-nav-item">
                        <a href="javascript:void(0)">{!! $_slug['description'] !!}</a>
                        <dl class="layui-nav-child">
                            @foreach($_slug['tables'] as $_table)
                                <dd><a href="#{!! $_table['table'] !!}">{!! $_table['description'] !!}</a>
                                </dd>
                            @endforeach
                        </dl>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <script>
	$(function() {
		layui.element.render();
		layui.element.on('nav(tree)', function(elem) {
			let $tree = $('#tree');
			console.log(Util.getViewport());
			let diff = Util.getViewport().height - $tree.height();
			if (diff < 5) {
				$tree.css({
					top : (Util.getViewport().height - $tree.height()) + 'px'
				})
			} else {
				$tree.css({
					top : '5px'
				})
			}

		});
	})
    </script>
@endsection