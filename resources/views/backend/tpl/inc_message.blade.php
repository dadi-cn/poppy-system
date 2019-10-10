@extends('system::backend.tpl.default')
@section('head-css')
    @include('system::backend.tpl._style')
@endsection
@section('body-class', 'gray-bg')
@section('body-main')
    @if(isset($input))
        {!!  Session::flashInput($input) !!}
    @endif
    <div class="layui-row layui-col-space10">
        <div class="layui-col-md4 layui-col-md-offset4">
            <fieldset class="layui-elem-field layui-field-title">
                <legend class="@if (Session::get('end.level') == 0 )  text-success @else text-danger  @endif">
                    @if (Session::get('end.level') === 0 )
                        <h3 class="panel-title"><i class="fa fa-check-circle-o"></i> 提示</h3>
                    @endif
                    @if (Session::get('end.level') === 1 )
                        <h3 class="panel-title"><i class="fa fa-times-circle-o"></i> 提示</h3>
                    @endif
                </legend>
                <div>
                    <div class="pt15 pb15 @if (Session::get('end.level') == 0 )  text-success @else text-danger  @endif" >
                        <p>{!! Session::get('end.message') !!}</p>
                    </div>
                    <p class="text-center">
                        @if (isset($location))
                            @if ($location == 'back' || $time == 0)
                                @if ($location != 'message')
                                    <a href="javascript:window.history.go(-1);">返回上级</a>
                                @endif
                            @else
                                您将在 <span id="clock">0</span>秒内跳转至目标页面, 如果不想等待, <a
                                        href="{!! $location !!}">点此立即跳转</a>!
                                <script>
								$(function() {
									var t = {!! $time !!};//设定跳转的时间
									setInterval(refer(), 1000); //启动1秒定时
									function refer() {
										if (t == 0) {
											window.location.href = "{!! $location !!}"; //设定跳转的链接地址
										}
										$('#clock').text(Math.ceil(t / 1000)); // 显示倒计时
										t -= 1000;
									}
								})
                                </script>
                            @endif
                        @endif
                    </p>
                </div>
            </fieldset>
        </div>
    </div>
@endsection