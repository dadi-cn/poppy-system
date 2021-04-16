<div class="layui-card">
    {{--  标题以及工具  --}}
    <div class="layui-card-header">
        {{ $title }}
        {{--显示工具--}}
        {!! $grid->renderHeaderTools() !!}

        <div class="pull-right">
            {!! $grid->renderQuickButton() !!}
        </div>
        @if ($grid->isShowExporter())
            {!! $grid->renderExportButton() !!}
        @endif
    </div>

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
    limit : {!! $grid->getPerPage() !!},
    // 使用后端排序
    autoSort : false,
    id : '{!! $filter_id !!}-table',
    loading : true,
    toolbar : true,
    even : true,
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
    let query  = {
        _query : 1
    };
    $.each(values, function(i, field) {
        query[field.name] = field.value;
    });
    layui.table.reload('{!! $filter_id !!}-table', {
        page : {
            curr : 1 //重新从第 1 页开始
        },
        where : query
    }, 'data');
    return false;
});

// 监听排序事件
// https://www.layui.com/doc/modules/table.html#onsort
layui.table.on('sort({!! $id !!}-filter)', function(obj) {
    layui.table.reload('{!! $filter_id !!}-table', {
        initSort : obj,
        where : {
            _field : obj.field,
            _order : obj.type,
            _query : 1
        }
    });
});

// 注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
// obj.value 得到修改后的值
// obj.field 当前编辑的字段名
// obj.data 所在行的所有相关数据
layui.table.on('edit({!! $id !!}-filter)', function(obj) {
    if (!obj.data['{!! $model_pk !!}']) {
        Util.splash({
            status : 1,
            message : '尚未定义/返回主键, 无法使用编辑功能'
        })
        return obj;
    }
    Util.makeRequest('{!! $url_base !!}', {
        '_edit' : 1,
        '_field' : obj.field,
        '_value' : obj.value,
        '_pk' : obj.data['{!! $model_pk !!}']
    })
});
</script>