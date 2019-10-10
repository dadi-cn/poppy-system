<?php namespace Poppy\System\Classes;

use Collective\Html\FormBuilder as CollectiveFormBuilder;
use Input;
use Poppy\Framework\Helper\FileHelper;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\TreeHelper;

/**
 * 表单生成
 */
class FormBuilder extends CollectiveFormBuilder
{
	/**
	 * 生成树选择
	 * @param string $name     名称
	 * @param array  $tree     需要生成的树
	 * @param string $selected 选择
	 * @param array  $options  选项
	 * @param string $id       ID KEY
	 * @param string $title    Title KEY
	 * @param string $pid      PID KEY
	 * @return string
	 */
	public function tree($name, $tree, $selected = '', $options = [], $id = 'id', $title = 'title', $pid = 'pid'): string
	{
		$formatTree = [];
		foreach ($tree as $tr) {
			$formatTree[$tr[$id]] = $tr;
		}
		$Tree = new TreeHelper();
		$Tree->init($formatTree, $id, $pid, $title);
		$treeArray = $Tree->getTreeArray(0);

		return $this->select($name, $treeArray, $selected, $options);
	}

	/**
	 * 数字微调器, 缺点: 无法和 Vue 一块使用
	 * @param string      $name    名字
	 * @param string|null $value   值
	 * @param array       $options 选项
	 * @return string
	 */
	public function spinner($name, $value = null, $options = []): string
	{
		$value     = (string) $this->getValueAttribute($name, $value);
		$id        = 'spinner_' . str_random(6);
		$attribute = $this->html->attributes($options);
		$code      = <<<CODE
<div id="{$id}" class="layui-spinner">
  <span type="button" data-spin="up" class="layui-spinner-up"><i class="fa fa-angle-up"></i></span>
  <input type="text" name="{$name}" value="{$value}" class="layui-input" {$attribute}>
  <span type="button" data-spin="down" class="layui-spinner-down"><i class="fa fa-angle-down"></i></span>
</div>
<script>
$(function () {
	$('#{$id}').spinner()
})
</script>
CODE;

		return $code;
	}

	/**
	 * radio 选择器(支持后台)
	 * @param string      $name    名字
	 * @param array       $lists   列表
	 * @param string|null $value   值
	 * @param array       $options 选项
	 * @return string
	 */
	public function radios($name, $lists = [], $value = null, $options = []): string
	{
		$str   = '';
		$value = (string) $this->getValueAttribute($name, $value);
		$id    = $options['id'] ?? 'radio_' . str_random(4);

		foreach ($lists as $key => $val) {
			$options['id']    = $id . '_' . $key;
			$options['title'] = $val;
			$str              .= $this->radio($name, $key, (string) $value === (string) $key, $options);
		}

		return $str;
	}

	/**
	 * 选择器
	 * @param string $name    名字
	 * @param array  $lists   数组
	 * @param null   $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function checkboxes($name, $lists = [], $value = null, $options = []): string
	{
		$str       = '';
		$arrValues = [];
		if (!$value) {
			$value = (string) $this->getValueAttribute($name, $value);
		}
		if (is_array($value)) {
			$arrValues = array_values($value);
		}
		elseif (is_string($value)) {
			if (strpos($value, ',') !== false) {
				$arrValues = explode(',', $value);
			}
			else {
				$arrValues = [$value];
			}
		}

		foreach ($lists as $key => $val) {
			$options['title']    = $val;
			$options['lay-skin'] = 'primary';
			$str                 .= $this->checkbox($name, $key, in_array($key, $arrValues, false), $options);
		}

		return $str;
	}

	/**
	 * 代码编辑器
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function code($name, $value = '', $options = []): string
	{
		$options['id'] = $this->getIdAttribute($name, $options) ?: 'code_' . str_random(5);
		$hiddenId      = $options['id'] . '_hidden';
		$hidden        = $this->hidden($name, $value, [
			'id' => $hiddenId,
		]);
		$value         = htmlentities($value);
		$html          = /** @lang text */
			<<<HTML
{$hidden}
<pre id="{$options['id']}" style="min-height: 100px;border:1px solid #ccc;">{$value}</pre>
<script>
	$(function(){
		var {$options['id']} = ace.edit("{$options['id']}");
		{$options['id']}.session.on('change', function() {
			$('#{$hiddenId}').val({$options['id']}.getValue())
		});
    });
</script>
HTML;

		return $html;
	}

	/**
	 * 编辑器
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function editor($name, $value = null, $options = []): string
	{
		$pam          = $options['pam'] ?? '';
		$token        = $pam ? app('tymon.jwt.auth')->fromUser($pam) : '';
		$uploadUrl    = route_url('system:api_v1.upload.image');
		$contentId    = 'editor_content_' . StrHelper::random('5');
		$defaultImage = url('assets/images/default/nopic.gif');
		$value        = (string) $this->getValueAttribute($name, $value);
		$data         = /** @lang text */
			<<<Editor
	<textarea class="hidden" name="{$name}" id="{$contentId}">{$value}</textarea>
		<script>
		$(function () {
			new Simditor({
				textarea: $('#{$contentId}'),
				defaultImage : '{$defaultImage}',
			    upload : {
					url : '{$uploadUrl}',
					params : {
						token : '{$token}',
					},
					fileKey : 'image',
					leaveConfirm: '上传进行中, 确认中断?'
			    },
			    toolbar: [
				  'title',
				  'bold',
				  'italic',
				  'underline',
				  'strikethrough',
				  'fontScale',
				  'color',
				  'ol',
				  'ul',
				  'blockquote',
				  'code',
				  'table',
				  'link',
				  'image',
				  'hr',
				  'indent',
				  'outdent',
				  'alignment',
				],
			    pasteImage: true,
			    cleanPaste : true
			});
		})
		</script>
Editor;

		return $data;
	}

	/**
	 * 生成排序链接
	 * @param string $name       名字
	 * @param string $value      值
	 * @param string $route_name 路由名字
	 * @param bool   $pjax       是否是 Pjax 请求
	 * @return string
	 */
	public function order($name, $value = '', $route_name = '', $pjax = false): string
	{
		$input = Input::all();
		$value = $value ?: ($input['_order'] ?? '');
		switch ($value) {
			case $name . '_desc':
				$con  = $name . '_asc';
				$icon = '<i class="fa fa-sort-down"></i>';
				break;
			case $name . '_asc':
				$con  = $name . '_desc';
				$icon = '<i class="fa fa-sort-up"></i>';
				break;
			default:
				$icon = '<i class="fa fa-sort"></i>';
				$con  = $name . '_asc';
		}
		$input['_order'] = $con;
		if ($route_name) {
			$link = route($route_name, $input);
		}
		else {
			$link = '?' . http_build_query($input);
		}
		$dp = $pjax ? 'data-pjax' : '';

		return '
			<a href="' . $link . '" ' . $dp . '>' . $icon . '</a>
		';
	}

	/**
	 * 提示组件
	 * @param string      $description 描述
	 * @param string|null $name        名字
	 * @return string
	 */
	public function tip($description, $name = null): string
	{
		if ($name === null) {
			$icon = '<i class="fa fa-question-circle">&nbsp;</i>';
		}
		else {
			$icon = '<i class="fa ' . $name . '">&nbsp;</i>';
		}
		$trim_description = strip_tags($description);

		return <<<TIP
<a title="{$trim_description}" class="J_dialog J_tooltip" data-title="信息提示" data-tip="{$trim_description}">
	{$icon}
</a>
TIP;
	}

	/**
	 * 上传缩略图
	 * @param string $name    名字
	 * @param null   $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function thumb($name, $value = null, $options = []): string
	{
		$id    = $this->getIdAttribute($name, $options) ?? 'thumb_' . str_random(6);
		$value = (string) $this->getValueAttribute($name, $value);
		$pam   = $options['pam'] ?? [];
		$token = $pam ? app('tymon.jwt.auth')->fromUser($pam) : '';

		$display_str = !$value ? 'class="hidden"' : '';
		$uploadUrl   = route('system:api_v1.upload.image');
		$parseStr    = /** @lang text */
			<<<CONTENT
<div class="system--form_thumb">
	<button id="{$id}" class="layui-btn" type="button">上传图片</button>
	<div class="form_thumb-ctr" id="{$id}_ctr">
	<input type="hidden" name="{$name}" value="{$value}" id="{$id}_url"/>
	<span id="{$id}_preview_ctr" {$display_str}>
		<span id="{$id}_preview" class="fa fa-image J_image_preview" data-src="{$value}" data-width="400" data-height="400"></span>
		<span id="{$id}_del" class="fa fa-times"></span>
	</span>
	</div>
</div>
<script>
layui.upload.render({
	elem: '#{$id}',
	url: '{$uploadUrl}',
	accept : 'images',
	field : 'image',
	size : 100000,
	data : {
	    token: '{$token}'
	},
	done: function(response){
		//上传完毕回调
		var obj_resp = Util.toJson(response);
		if (obj_resp.status !== 0) {
		    Util.splash(obj_resp);
		} else {
			$('#{$id}_url').val(obj_resp.data.url[0]);
			$('#{$id}_preview_ctr').removeClass('hidden');
			$('#{$id}_preview').attr('data-src', obj_resp.data.url[0]);
		}
		$("#{$id}_preview_ctr").show();
	},
	error: function(){
	  //请求异常回调
	}
});
	$("#{$id}_del").click(function () {
		$("#{$id}_preview_ctr").hide();
		$("input[name={$name}]").val('');
	});
</script>
CONTENT;

		return $parseStr;
	}

	/**
	 * 上传缩略图
	 * @param string $name    名字
	 * @param null   $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function upload($name, $value = null, $options = []): string
	{
		$id    = $this->getIdAttribute($name, $options) ?? 'upload_' . str_random(6);
		$value = (string) $this->getValueAttribute($name, $value);
		$pam   = $options['pam'] ?? [];
		$type  = $options['type'] ?? 'images';
		if (!in_array($type, ['images', 'audio', 'video', 'file'])) {
			$type = 'images';
		}
		$token = $pam ? app('tymon.jwt.auth')->fromUser($pam) : '';

		/* 进行赋值
		 * ---------------------------------------- */
		if ($value) {
			switch ($type) {
				case 'images':
				default:
					$template = '<!--图片-->
					<a data-fancybox="system.upload" href="___VALUE___">
						<img style="position: relative;top: 2px;" alt="" height="30" src="___VALUE___">
					</a>';
					break;
				case 'audio':
					$template = '<!--音频-->
						<audio style="height: 30px;position: relative;top: 11px;" controls>
							<source src="___VALUE___" type="audio/mp3">
						</audio>';
					break;
				case 'video':
					$template = '<!--视频-->
					<a data-fancybox="system.upload" href="___VALUE___">
						<i class="fa fa-video"></i>
					</a>';
					break;
				case 'file':
					$template = '<!--视频-->
					<a target="_blank" href="___VALUE___">
						<i class="fa fa-file"></i>
					</a>';
					break;
			}
		}
		else {
			$template = '';
		}
		$content  = str_replace(['___VALUE___', PHP_EOL], [$value, ''], $template);
		$template = str_replace(["\n", "\t", PHP_EOL], '', $template);

		$display_str = !$value ? 'class="hidden"' : '';
		$uploadUrl   = route('system:api_v1.upload.file');
		$parseStr    = /** @lang text */
			<<<CONTENT
<div class="system--form_upload">
	<button id="{$id}" class="layui-btn layui-btn-sm" type="button">上传</button>
	<div class="form_thumb-ctr" id="{$id}_ctr">
		<input type="hidden" name="{$name}" value="{$value}" id="{$id}_url"/>
		<span id="{$id}_preview_ctr" {$display_str}>
			<span id="{$id}_content">
				{$content}
			</span>
			<span id="{$id}_del" class="fa fa-times"></span>
		</span>
	</div>
</div>
<script>
var {$id}_tpl = '{$template}';
layui.upload.render({
	elem: '#{$id}',
	url: '{$uploadUrl}',
	accept : '{$type}',
	field : 'file',
	size : 100000,
	data : {
	    token: '{$token}',
	    type: '{$type}',
	},
	done: function(response){
		//上传完毕回调
		var obj_resp = Util.toJson(response);
		if (obj_resp.status !== 0) {
		    Util.splash(obj_resp);
		} else {
			$('#{$id}_url').val(obj_resp.data.url[0]);
			$('#{$id}_preview_ctr').removeClass('hidden');
			{$id}_tpl = {$id}_tpl.replace(/___VALUE___/g, obj_resp.data.url[0])
			$('#{$id}_content').html({$id}_tpl);
		}
		$("#{$id}_preview_ctr").show();
	},
	error: function(){
	  //请求异常回调
	}
});
	$("#{$id}_del").click(function () {
		$("#{$id}_preview_ctr").hide();
		$("input[name={$name}]").val('');
	});
	$('[data-fancybox="system.upload"]').fancybox({});
</script>
CONTENT;

		return $parseStr;
	}

	/**
	 * 多图上传组件
	 * @param string $name    form 名称
	 * @param null   $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function multiThumb($name, $value = null, $options = []): string
	{
		$id       = $this->getIdAttribute($name, $options) ?? 'multi_thumb_' . str_random(6);
		$number   = $options['number'] ?? 3;
		$pop_size = $options['pop_size'] ?? '300';
		$type     = $options['type'] ?? 'image';
		$sequence = $options['sequence'] ?? false;
		$ext      = 'jpg|png|gif|jpeg|webp';
		if ($type === 'video') {
			$ext = 'mp4';
		}
		if ($type === 'picture') {
			$ext = 'mp4|jpg|png|gif|jpeg|webp';
		}
		$value = (array) $this->getValueAttribute($name, $value);
		if (strpos($name, '[]') === false) {
			$name .= '[]';
		}
		$auto       = (bool) ($options['auto'] ?? false);
		$autoEnable = $auto ? 'true' : 'false';
		$renderStr  = '';
		if (count($value)) {
			$data      = json_encode($value);
			$renderStr = <<<HAHA
			//将预览html 追加
			var values = {$data};
			for(var item in values) {
				var data = {
					index : item,
					name  : item,
					type  : (values[item].indexOf('.mp4') !== -1) ? 'video' : 'image',
					result : values[item],
					classname : 'multi-uploaded',
				}
				layui.laytpl({$id}_template.innerHTML).render(data, function (html) {
					$('#{$id}_container').append(html);
				});
			}
HAHA;
		}
		$sequenceStr = '';
		if ($sequence) {
			$sequenceStr = '<input type="text" name="_multi_sequence[]" class="layui-input w36">';
		}
		$pam        = $options['pam'] ?? [];
		$token      = $pam ? app('tymon.jwt.auth')->fromUser($pam) : '';
		$uploadUrl  = route('system:api_v1.upload.image');
		$autoUpload = $auto ? '' : '<button type="button" class="layui-btn layui-btn-sm" id="' . $id . '_upload" disabled>开始上传</button>';
		$data       = /** @lang text */
			<<<MULTI
<div class="layui-upload upload--multi">
    <div class="layui-btn-group">
        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="{$id}_select">选择文件</button>
        {$autoUpload}
        <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="{$id}_delete">删除选中图片</button>
    </div>
    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
        <div class="layui-upload-list clearfix" id="{$id}_container"></div>
    </blockquote>
</div>
<script id="{$id}_template" type="text/html">
    <div class="multi-img {{ d.classname }}" filename="{{ d.index }}">
        <i class="fa fa-check" style="display:none;"></i>
        <input type="checkbox" name="________mark" lay-ignore>
        <input type="checkbox" class="j_img_value" checked name="{$name}" value="{{  d.result }}" lay-ignore>
        {{#  if(d.type === 'image'){ }}
	    <img src="{{  d.result }}" alt="{{ d.name }}" class="layui-upload-img J_image_preview" data-width="{{ $pop_size }}px" data-height="{{ $pop_size }}px">
        {{# } else { }}
        <video controls class="layui-upload-img">
            <source src="{{  d.result }}" type="video/mp4">
        </video>
        {{#  } }} 
	    {$sequenceStr}
    </div>
</script>
<script>
$(function(){
	var {$id}_files = [];
	
	{$renderStr}
	
	 //绑定单击事件
	$('body').on('click', '#{$id}_container>div',  function () {
		var isChecked = $(this).find("input[name=________mark]").prop("checked");
		$(this).find("input[name=________mark]").prop("checked", !isChecked);
		if (isChecked) {
			$(this).removeClass('multi-checked');
		} else {
			$(this).addClass('multi-checked')
		}
		return false;
	});
	var {$id}_uploader = layui.upload.render({
	    elem:'#{$id}_select',   //开始
	    url: '{$uploadUrl}' ,
	    multiple: true,
	    number : {$number},
	    auto: {$autoEnable},
	    bindAction: '#{$id}_upload',
	    accept : 'file',
		field : 'image',
		exts : '{$ext}',
	    size : 100000,
		data : {
		    token : '{$token}'
		},
	    choose: function (obj) {  //选择图片后事件
			var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
            {$id}_files = files;
	        $('#{$id}_upload').prop('disabled',false);
	        //预读本地文件示例，不支持ie8
	        obj.preview(function (index, file, result) {
	            var data = {
	                index: index,
	                name: file.name,
	                type: (file.name.indexOf('.mp4') !== -1) ? 'video' : 'image',
	                result: result,
	                classname : ''
	            };
	            var length = $('#{$id}_container div').length;
	            if (length>={$number}){
	            	delete {$id}_files[index];
	            	top.layer.msg('添加的图片不能多于 {$number} 张');
	            	return;
	            }
	            if ($('#{$id}_container').html()=== '请选择图片') {
	            	$('#{$id}_container').html('');
	            }
	            //将预览html 追加
	            layui.laytpl({$id}_template.innerHTML).render(data, function (html) {
	                $('#{$id}_container').append(html);
	            });
	        });
	     }, 
	    before: function (obj) { //上传前回函数
			if (!{$id}_files.length){
			     top.layer.msg("无可以上传文件, 请选择文件！");
			     return;
			}
	        layer.load(); //上传loading
	    },
	    done: function (res,index,upload) {    //上传完毕后事件
	        var ctr = $('#{$id}_container').find('[filename='+index+']');
	        
	        ctr.find('img').attr('src', res.data.url[0]);
	        ctr.find('.j_img_value').attr('value', res.data.url[0]);
	        ctr.addClass('multi-uploaded');
	        layer.closeAll('loading'); //关闭loading
	        top.layer.msg("上传成功！");
	        return delete {$id}_files[index]; // 删除文件队列已经上传成功的文件
	    }, 
	    error: function (index, upload) {
	        layer.closeAll('loading'); //关闭loading
	        top.layer.msg("上传失败！");
	    }
	})
	//批量删除 单击事件
	$('#{$id}_delete').click(function () {
	    $('#{$id}_container').find('input[name=________mark]:checked').each(function (index, value) {
	        var filename = $(this).parent().attr("filename");
	        delete {$id}_files[filename];
	        $(this).parent().remove();
	        if (!$.trim($('#{$id}_container').html())){
	            $('#{$id}_container').text('请选择图片');
	        }
	    });
	});
})
</script>
MULTI;

		return $data;
	}

	/**
	 * 显示上传的单图
	 * @param string|array $url     需要显示的地址
	 * @param array        $options 选项
	 * @return string
	 */
	public function showThumb($url, array $options = []): string
	{
		$size       = $options['size'] ?? 'sm';
		$pop_size   = $options['pop_size'] ?? '300';
		$strOptions = $this->html->attributes($options);
		$style      = '';
		if ($size === 'xs') {
			$style = 'max-width:32px;max-height:32px;';
		}
		if ($size === 'sm') {
			$style = 'max-width:50px;max-height:50px;';
		}
		if ($size === 'l') {
			$style = 'max-width:80px;max-height:80px;';
		}
		if ($size === 'xl') {
			$style = 'max-width:120px;max-height:120px;';
		}
		if ($size === 'ori') {
			$style = '';
		}
		if (is_string($url) || is_null($url)) {
			$url = $url ?: '/assets/images/default/nopic.gif';


			$parse_str = '<img class="J_image_preview" data-width="' . $pop_size . 'px" data-height="' . $pop_size . 'px" src="' . $url . '" ' . $strOptions . '
		 style="' . $style . '">';
			return $parse_str;
		}

		$parse_str = '<div class="clearfix layui-upload upload--multi">';
		foreach ($url as $_url) {
			$ext       = FileHelper::ext($_url);
			$parse_str .= '<div class="multi-img" style="' . $style . '">';
			if ($ext === 'mp4') {
				$parse_str .= '<video controls class="layui-upload-img" style="' . $style . '">
		            <source src="' . $_url . '" type="video/mp4">
		        </video>';

			}
			else {
				$parse_str .= '<img src="' . $_url . '" class="layui-upload-img J_image_preview" data-width="' . $pop_size . 'px" data-height="' . $pop_size . 'px" style="' . $style . '">';
			}
			$parse_str .= '</div>';
		}

		$parse_str .= '</div>';
		return $parse_str;
	}

	/**
	 * 日期选择器
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function timePicker($name, $value = '', $options = []): string
	{
		$options['id'] = $this->getIdAttribute($name, $options) ?: 'time_picker_' . str_random(4);
		$value         = (string) $this->getValueAttribute($name, $value);

		$options['class'] = 'layui-input ' . ($options['class'] ?? '');
		$attr             = $this->html->attributes($options);

		$html = <<<HTML
<input type="text" name="{$name}" value="{$value}" {$attr}>
<script>
	$(function(){
		layui.laydate.render({
			elem : '#{$options['id']}', 
			type : 'time'
		});
	});
</script>
HTML;

		return $html;
	}

	/**
	 * 生成日期时间选择器
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function datetimePicker($name, $value = '', $options = []): string
	{
		$options['id'] = $this->getIdAttribute($name, $options) ?: 'datetime_picker_' . str_random(4);
		$value         = (string) $this->getValueAttribute($name, $value);

		$options['class'] = 'layui-input ' . ($options['class'] ?? '');
		$attr             = $this->html->attributes($options);

		$html = <<<HTML
<input type="text" name="{$name}" value="{$value}" {$attr}>
<script>
	$(function(){
		layui.laydate.render({
			elem : '#{$options['id']}', 
			type : 'datetime'
		});
	});
</script>
HTML;

		return $html;
	}

	/**
	 * 生成日期选择器
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function datePicker($name, $value = '', array $options = []): string
	{
		$options['id'] = $this->getIdAttribute($name, $options) ?: 'date_picker_' . str_random(4);
		$value         = (string) $this->getValueAttribute($name, $value);

		$options['class'] = 'layui-input ' . ($options['class'] ?? '');
		$attr             = $this->html->attributes($options);

		$html = <<<HTML
<input type="text" name="{$name}" value="{$value}" {$attr}>
<script>
	$(function(){
		layui.laydate.render({
			elem: '#{$options['id']}'
		});
	});
</script>
HTML;

		return $html;
	}

	/**
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function dateRangePicker($name, $value = '', $options = []): string
	{
		$options['id'] = $this->getIdAttribute($name, $options) ?: 'daterange_picker_' . str_random(4);
		$value         = (string) $this->getValueAttribute($name, $value);

		$options['class'] = 'layui-input ' . ($options['class'] ?? '');
		$attr             = $this->html->attributes($options);

		$html = <<<HTML
<input type="text" name="{$name}" value="{$value}" {$attr}>
<script>
	$(function(){
		layui.laydate.render({
			elem  : '#{$options['id']}',
			range : true
		});
	});
</script>
HTML;

		return $html;
	}

	/**
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function monthPicker($name, $value = '', $options = []): string
	{
		$options['id'] = $this->getIdAttribute($name, $options) ?: 'month_picker_' . str_random(4);
		$value         = (string) $this->getValueAttribute($name, $value);

		$options['class'] = 'layui-input ' . ($options['class'] ?? '');
		$attr             = $this->html->attributes($options);

		$html = <<<HTML
<input type="text" name="{$name}" value="{$value}" {$attr}>
<script>
	$(function(){
		layui.laydate.render({
			elem  : '#{$options['id']}',
			type : 'month'
		});
	});
</script>
HTML;

		return $html;
	}

	/**
	 * @param string $name    名字
	 * @param string $value   值
	 * @param array  $options 选项
	 * @return string
	 */
	public function colorPicker($name, $value = '', $options = []): string
	{
		$options['id']    = $this->getIdAttribute($name, $options) ?: 'colorpicker_' . str_random(5);
		$value            = (string) $this->getValueAttribute($name, $value);
		$options['class'] = 'layui-input ' . ($options['class'] ?? '');
		$attr             = $this->html->attributes($options);
		$html             = <<<HTML
<div class="layui-input-inline" style="width: 120px;">
	<input type="text" id="input_{$options['id']}" name="{$name}" value="{$value}" placeholder="请选择颜色" {$attr}>
</div>
<div class="layui-inline" style="left: -4px;">
	<div id="{$options['id']}"></div>
</div>
<script>
	$(function(){
		layui.colorpicker.render({
            elem  : '#{$options['id']}',
            color : '{$value}',
            done  : function(color){
                $('#input_{$options['id']}').val(color);
            }
        })
    });
</script>
HTML;

		return $html;
	}

	/**
	 * 显示UI, 减少传值
	 * @param string $name         UI Key
	 * @param array  $route_params 路由参数
	 * @param bool   $display      是否显示
	 * @return string
	 */
	public function ui($name, $route_params = [], $display = true): string
	{
		return app('module')->uis()->render($name, $route_params, $display);
	}
}
