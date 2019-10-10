<!-- 侧边菜单 -->
<div class="layui-side layui-side-menu" data-pjax pjax-ctr="#main">
    <div class="layui-side-scroll" id="menu">
        @if (isset($_menus))
            <ul class="layui-nav layui-nav-tree"
                lay-shrink="all"
                id="LAY-system-side-menu"
                lay-filter="admin-side-nav"
            >
                @foreach($_menus as $k_menu => $v_menu)
                    @foreach($v_menu['groups'] as $k_group => $v_group)
                        <li data-name="{!! $k_group !!}"
                            class="layui-nav-item {!! active_class($v_group['routes']->contains($_route), 'layui-nav-itemed') !!}">
                            <a href="#" class="collapsible-header waves-effect J_ignore" lay-tips="{{$v_group['title']}}" lay-direction="2">
                                {!! isset($v_group['icon']) && $v_group['icon']? '<i class="'.$v_group['icon'].'"></i>' :'' !!}
                                <cite>{{$v_group['title']}}</cite>
                                <span class="layui-nav-more"></span>
                            </a>
                            <dl class="layui-nav-child">
                                @foreach($v_group['children'] as $v_link)
                                    @if (isset($v_link['route']))
                                        <dd class=" @if ($v_link['route']??'' === $_route || in_array($_route, $v_link['match']??[], true)) layui-this @endif">
                                            <a lay-href="{{ route_url($v_link['route'])}}" class="waves-effect J_ignore">
                                                {!! isset($v_link['icon']) && $v_link['icon']? '<i class="'.$v_link['icon'].'"></i>' :'' !!}
                                                {{$v_link['title']}}
                                            </a>
                                        </dd>
                                    @else
                                        @if($v_link['children']??[])
                                        <dd>
                                            <a class="J_ignore">
                                                {!! isset($v_link['icon']) && $v_link['icon']? '<i class="'.$v_link['icon'].'"></i>' :'' !!}
                                                {!! $v_link['title'] !!}
                                            </a>
                                            <dl class="layui-nav-child">
                                                @foreach ($v_link['children'] as $c_link)
                                                    <dd>
                                                        <a lay-href="{{ route_url($c_link['route'])}}" class="J_ignore">
                                                            {!! isset($c_link['icon']) && $c_link['icon']? '<i class="'.$c_link['icon'].'"></i>' :'' !!}
                                                            {!! $c_link['title'] !!}
                                                        </a>
                                                    </dd>
                                                @endforeach
                                            </dl>
                                        </dd>
                                        @endif
                                    @endif
                                @endforeach
                            </dl>
                        </li>
                    @endforeach

                @endforeach
            </ul>
        @endif
    </div>
</div>