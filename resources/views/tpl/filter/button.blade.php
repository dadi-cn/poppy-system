<span class="header-filter-switch" id="{!! $filter_id !!}">
    <i class="fa {!! $expand ? 'fa-caret-down' : 'fa-caret-right' !!}" id="{!! $filter_id !!}-icon"></i>
</span>

@if($scopes->isNotEmpty())
    <div class="dropdown-menu">
        <button class="layui-btn icon-btn layui-btn-sm">
            &nbsp;{!! $current_label?:'选择' !!} <i class="layui-icon layui-icon-drop"></i>
        </button>
        <ul class="dropdown-menu-nav">
            @foreach($scopes as $scope)
                {!! $scope->render() !!}
            @endforeach
        </ul>
    </div>
    <script>
    layui.use(['dropdown']);
    </script>
@endif
<script>
$(function() {
    $('#{!! $filter_id !!}').on('click', function() {
        let $filterIcon = $('#{!! $filter_id !!}-icon');
        let $filterForm = $('#{!! $filter_id !!}-form-ctr');
        let display     = $filterForm.css('display');
        if (display !== 'none') {
            $filterForm.css('display', 'none');
            $filterIcon.removeClass('fa-caret-down').addClass('fa-caret-right')
        } else {
            $filterForm.css('display', 'block');
            $filterIcon.removeClass('fa-caret-right').addClass('fa-caret-down');
        }
    })
})
</script>
