@extends('poppy-system::backend.tpl.dialog')
@section('backend-main')
    @if ($total > 0)
        <div class="alert alert-info">本次需要更新 <strong>{{$total}}</strong> 条信息, 每批次更新 <strong>{{$section}}</strong> 条, 还剩余
            <strong>{{$left}}</strong>条
        </div>
        @if (isset($info))
            <div class="alert alert-warning">{!! $info !!}</div>
        @endif
        <div class="progress">
            <div class="progress-bar bg-success progress-bar-striped text-warning"
                 role="progressbar" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100"
                 style="width:{{$percentage}}%">
                {{$percentage}}%
            </div>
        </div>

        @if ($left === 0)
            <div class="alert alert-success">更新成功</div>
            <script>
			// over progress close it
			Util.splash({
				status : 0,
				msg    : '更新成功!'
			});
            </script>
        @else
            <script>
			setTimeout("window.location.href = '{!!$continue_url!!}'", {{$continue_time}});
            </script>
        @endif
    @else
        <div class="alert alert-warning">没有需要更新的内容</div>
    @endif
@endsection