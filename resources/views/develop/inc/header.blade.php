<ul class="layui-nav layui-bg-cyan dev--nav">
    <li class="layui-nav-item">
        <a href="{!! route('system:develop.cp.cp') !!}">
            <i class="fa fa-home"></i>
        </a>
    </li>
    @foreach($_menus as $key => $menu)
        <li class="layui-nav-item">
            <a href="{!! route('system:develop.cp.cp') !!}#{!! md5($menu['title']??'') !!}">
                {!! $menu['title'] !!}
            </a>
        </li>
    @endforeach
	<?php use Poppy\System\Models\PamAccount;$pam = Auth::guard(PamAccount::GUARD_DEVELOP)->user() ?>
    @if ($pam)
        <li class="layui-nav-item pull-right">
            <a href="{!! route('system:develop.pam.logout') !!}" class="J_tooltip J_request" title="退出登录">
                <i class="fa fa-bomb"></i>
            </a>
        </li>
    @endif
</ul>