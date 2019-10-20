<div class="layui-card-header">
    {!! $title !!}
    <div class="pull-right">
        @foreach($tools as $tool)
            {!! $tool !!}
        @endforeach
    </div>
</div>
<div class="layui-card-body">
    {!! $content !!}
</div>