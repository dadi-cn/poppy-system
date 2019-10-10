<div class="layui-header">
    <div class="layui-logo" lay-href="{!! route('system:backend.home.cp') !!}">
        {!! sys_setting('system::site.name') !!}
    </div>
    <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:" layadmin-event="flexible" ew-event="flexible" title="侧边伸缩" class="J_ignore">
                <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="{!! url('/') !!}" target="_blank" title="前台" class="J_ignore">
                <i class="layui-icon layui-icon-website"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="javascript:" layadmin-event="refresh" ew-event="refresh" title="刷新" class="J_ignore">
                <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
        </li>
        {{--
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search" layadmin-event="serach"
                   lay-action="template/search.html?keywords=">
        </li>
        --}}
    </ul>
    {!! sys_hook('system.html_top_nav') !!}
    <ul class="layui-nav layui-layout-right" data-pjax pjax-ctr="#main">
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="#" layadmin-event="note" ew-event="note"  class="J_ignore">
                <i class="layui-icon layui-icon-note"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect="">
            <a ew-event="theme"  class="J_ignore">
                <i class="layui-icon layui-icon-theme"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a ew-event="fullScreen" title="全屏"  class="J_ignore">
                <i class="layui-icon layui-icon-screen-full"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="#" class="J_ignore">
                <cite>{{$_pam->username ?? ''}}</cite>
                <span class="layui-nav-more"></span>
            </a>
            <dl class="layui-nav-child">
                <dd><a ew-href="{!! route('system:backend.home.password') !!}" class="J_ignore">修改密码</a></dd>
                @if(!is_production())
                    <dd><a ew-href="{!! route('system:backend.home.fe') !!}" class="J_ignore">后台帮助</a></dd>
                @endif
                <dd style="text-align: center;">
                    <a href="{!! route('system:backend.home.logout') !!}" class="J_ignore">退出</a>
                </dd>
            </dl>
        </li>
    </ul>
</div>