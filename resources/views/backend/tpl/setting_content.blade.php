@if(count($pages))
    <ul class="layui-tab-title mg8 pl8 pr8">
        @foreach($pages as $key => $conf)
            <li class="{!! active_class($key === $path, 'layui-this') !!}">
                <a href="{!! route('system:backend.home.setting', [$key]) !!}">
                    {!! $conf['title'] !!}
                </a>
            </li>
        @endforeach
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            @endif
            <div class="layui-tab layui-tab-brief" style="{!! !count($pages)? 'margin:0': '' !!}">
                <ul class="layui-tab-title">
					<?php $i = 0 ?>
                    @foreach($tabs as $group_key => $group)
                        <li class="<?php echo $i++ == 0 ? 'layui-this' : ''; ?>">{!! $group['title'] ?? '其他'  !!}</li>
                    @endforeach
                </ul>
                <div class="layui-tab-content">
					<?php $i = 0 ?>
                    @foreach($tabs as $group_key => $group)
                        <div class="layui-tab-item <?php echo $i++ == 0 ? 'layui-show' : ''; ?>"
                             id="{!! $group_key !!}">
                            {!! Form::open(['url' => $url,'id' => 'form_'.$group_key,'class'=> 'layui-form']) !!}
                            {!! Form::hidden('_group', $group_key) !!}
                            @foreach($group['fields'] as $item_key => $item)
                                <div class="layui-form-item">
                                    <label for="" class="layui-form-label
                                            {!! (isset($item['validates']['required']) &&  $item['validates']['required']) ? 'validation' :'' !!}">
                                        {!! $item['label'] !!}
                                    </label>
                                    <div class="layui-input-block">
                                        @include('poppy-system::backend.tpl._render')
                                    </div>
                                </div>
                            @endforeach
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    {!! Form::button('提交',['class'=>'layui-btn','lay-filter'=> 'form_'.$group_key, 'lay-submit'=> '']) !!}
                                </div>
                            </div>
                            {!!Form::close()!!}
                            <script>
							layui.form.on('submit({!! 'form_'.$group_key !!})', function(data) {
								Util.makeRequest('{!! $url !!}', data.field);
								return false;
							});
                            </script>
                        </div>
                    @endforeach
                </div>
            </div>
            @if(count($pages))
        </div>
    </div>
@endif
<script>
layui.form.render();
</script>