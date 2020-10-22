<div class="layui-row layui-col-space15 mt15">
    <div class="layui-col-xs3" style="line-height: 2;">
        @include('poppy-system::develop.layout._side')
    </div>
    <div class="layui-col-xs9">
        {!! Form::open(['class'=> 'layui-form']) !!}
        <fieldset class="layui-elem-field layui-field-title">
            <legend>图片上传组件(如果未开启,请先检查是否加载了 Flash 控件)</legend>
        </fieldset>
        <div>
            {!! Form::thumb('test') !!} <br> 这里的 pam 必须传递, 作为上传图片时候的身份验证
        </div>
        <pre class="layui-code"><?php echo '{{' ?> Form::thumb('thumb', null, ['pam' => $pam])}}</pre>
        {{--生成图片地址--}}
        <pre class="layui-code"><?php echo '{!! ' ?> Form::monthPicker('month', null, ['class' => 'layui-input']) !!}</pre>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>图片地址(生成随机图片地址, 布局使用)</legend>
        </fieldset>
        <div>
            {!! Html::image($faker->imageUrl(100, 50)) !!}
        </div>
        <pre class="layui-code"><?php echo '{!! ' ?> Html::image($faker->imageUrl(100, 50)) !!}</pre>
        {{--生成图片地址--}}
        <fieldset class="layui-elem-field layui-field-title">
            <legend>多图上传</legend>
        </fieldset>
        <div>
            {!! Form::multiThumb('images', [
                'https://oss-test.iliexiang.com/static/demo/dabai.jpg',
                'https://oss-test.iliexiang.com/static/demo/holiday.mp4',
            ], [
                'type' => 'picture',
                'sequence' => true,
            ]) !!}
        </div>
        <pre class="layui-code"><?php echo '{!! ' ?> Form::multiThumb('images', []) !!}</pre>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>多图展示</legend>
        </fieldset>
        <div>
            {!! Form::showThumb([
                'https://oss-test.iliexiang.com/static/demo/dabai.jpg',
                'https://oss-test.iliexiang.com/static/demo/holiday.mp4',
            ], [
                'size' => 'xl',
            ]) !!}
        </div>
        <pre class="layui-code"><?php echo '{!! ' ?> Form::showThumb([
    'https://oss-test.iliexiang.com/static/demo/dabai.jpg',
    'https://oss-test.iliexiang.com/static/demo/holiday.mp4',
], [
    'size' => 'xl',
]) !!}</pre>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>资源上传</legend>
        </fieldset>
        <div>
            {!! Form::upload('files', 'https://oss-test.iliexiang.com/static/demo/dev.rp', [
                'type' => 'file'
            ]) !!}
        </div>
        <pre class="layui-code"><?php echo '{!! ' ?> Form::upload('files', 'https://oss-test.iliexiang.com/static/demo/dev.rp', [
    'type' => 'file'
]) !!}</pre>
        {!! Form::close() !!}
        <script>
		layui.form.render();
        </script>
    </div>
</div>
<div class="mb100"></div>
