<div class="layui-card">
    @if(isset($title))
        <div class="layui-card-header">
            {{ $title }}
            {{--显示工具--}}
            @if ( $grid->showTools() )
                {!! $grid->renderHeaderTools() !!}
            @endif
            <div class="pull-right">
                {!! $grid->renderQuickButton() !!}
            </div>
            @if ( $grid->showTools() && $grid->isShowExporter())
                {!! $grid->renderExportButton() !!}
            @endif
        </div>
    @endif

    <div class="layui-card-body">

        {{-- 首选范围 --}}
        @if($scopes->count())
            <div class="layui-tab" style="margin-bottom: 10px;">
                <ul class="layui-tab-title">
                    @foreach($scopes as $scope)
                        {!! $scope->render() !!}
                    @endforeach
                </ul>
            </div>
        @endif

        {{--  显示查询条件  --}}
        {!! $grid->renderFilter() !!}

        {{-- Layui Table--}}
        <table class="layui-hide" id="{!! $id !!}" lay-filter="{!! $id !!}-filter"></table>
    </div>
</div>


<script>
layui.table.render($.extend({!! $lay !!}, {
    // 返回的数据去做解析
    request : {
        limitName : 'pagesize'
    },
    // 使用后端排序
    autoSort : false,
    id : '{!! $filter_id !!}-table',
    loading : true,
    parseData : function(resp) {
        return {
            code : resp.status,
            msg : resp.message,
            count : resp.data.pagination.total,
            data : resp.data.list
        };
    }
}));
$('#{!! $filter_id !!}-reload').on('click', function() {
    let values = $('#{!! $filter_id !!}-form').serializeArray();
    let query  = {};
    $.each(values, function(i, field) {
        query[field.name] = field.value;
    });
    layui.table.reload('{!! $filter_id !!}-table',
        {
            page : {
                curr : 1 //重新从第 1 页开始
            },
            where : query
        },
        'data'
    );
    return false;
});
// 监听排序事件
// https://www.layui.com/doc/modules/table.html#onsort
layui.table.on('sort({!! $id !!}-filter)', function(obj) {
    layui.table.reload('{!! $filter_id !!}-table', {
        initSort : obj,
        where : {
            _field : obj.field,
            _order : obj.type
        }
    });
});
</script>