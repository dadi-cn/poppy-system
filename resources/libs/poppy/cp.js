/**
 * Fe控制
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2018 Sour Lemon Team
 */
(function() {

	$(function() {

		var $body = $('body');

		if ($.fn.tooltip){
			$('.J_tooltip').tooltip();
		}

		if (typeof moment !== 'undefined') {
			moment.locale('zh-cn');
		}


		// 对话框, 用于显示信息提示
		// 不能用于生成图片组件
		// @see http://stackoverflow.com/questions/12271105/swfupload-startupload-fails-if-not-called-within-the-file-dialog-complete-hand
		$body.on('click', '.J_dialog', function(e) {
			// confirm
			var tip = $(this).attr('data-tip');
			var element = $(this).attr('data-element');
			var title = $(this).attr('data-title') ? $(this).attr('data-title') : $(this).html();
			var width = parseInt($(this).attr('data-width')) ? parseInt($(this).attr('data-width')) : 400;
			var height = parseInt($(this).attr('data-height')) ? parseInt($(this).attr('data-height')) : '';
			var area = height ? [width + 'px', height + 'px'] : width + 'px';

			// 获取到元素的 html, 并且存入到当前元素
			if (element) {
				tip = $(element).html();
				$(this).attr('data-tip', tip);
			}

			// open with layer
			layer.open({
				// type   : 1,
				title   : title,
				content : tip,
				area    : area,
				btn     : []
			});
			e.preventDefault();
		});

		// 弹出 iframe url
		$body.on('click', '.J_iframe', function(e) {
			var $this = $(this);
			// confirm
			var href = $(this).attr('href');
			if (!href) {
				href = $(this).attr('data-href');
			}
			var title = $(this).attr('data-title') ? $(this).attr('data-title') : '';
			if (!title) {
				title = $(this).attr('title') ? $(this).attr('title') : '';
			}
			if (!title) {
				title = $(this).attr('data-original-title') ? $(this).attr('data-original-title') : $(this).html();
			}
			var width = parseInt($(this).attr('data-width')) ? parseInt($(this).attr('data-width')) : '500';
			var height = parseInt($(this).attr('data-height')) ? parseInt($(this).attr('data-height')) : '500';
			var shade_close = $(this).attr('data-shade_close') !== 'false';
			var append = $this.attr('data-append');
			var data = Util.appendToObj(append);
			data._iframe = 'poppy';
			href = Util.objToUrl(data, href);
			layer.open({
				type       : 2,
				content    : href,
				area       : [width + 'px', height + 'px'],
				title      : title,
				shadeClose : shade_close
			});
			e.preventDefault();
			return false;
		});

		// 全选 start
		$body.on('click change', '.J_check_all', function() {
			if (this.checked) {
				$(".J_check_item").prop('checked', true)
			} else {
				$(".J_check_item").prop('checked', false)
			}
		});

		// 确定 请求后台操作, POST 方法
		$body.on('click', '.J_request', function(e) {
			var $btn = $(this);
			Util.buttonInteraction($btn, 5);
			layer.load(3, {
				shade : [0.1, '#000']
			});
			Util.requestEvent($(this), function(data) {
				Util.splash(data);
				Util.buttonInteraction($btn, data);
				layer.closeAll();
			});
			e.preventDefault();
		});


		// 图片预览
		$body.on('click', '.J_image_preview', function(e) {
			var _src = $(this).attr('src');
			if (typeof _src !== 'undefined' && _src.indexOf('nopic') >= 0) {
				return;
			}
			if (!_src) {
				_src = $(this).attr('data-src');
			}
			var h = parseInt($(this).attr('data-height'));
			var w = parseInt($(this).attr('data-width'));
			if (e.ctrlKey) {
				window.open($(this).attr('src'), '_blank')
			} else {
				if (!_src) {
					Util.splash({
						status  : 1,
						message : '没有图像文件!'
					});
					return false;
				}
				Util.imageSize(_src, _popup_show);

				if (!w) {
					w = parseInt(screen.width * 0.7);
				}

				/**
				 * imgObj.width   imgObj.height  imgObj.url
				 * @param imgObj
				 * @private
				 */
				function _popup_show(imgObj) {
					var _w = imgObj.width;
					var _h = imgObj.height;
					if (_w > h) {
						if (typeof h !== 'undefined' && imgObj.height > h) {
							_h = h;
							_w = parseInt(_h * imgObj.width / imgObj.height);
						}
					} else {
						if (typeof w !== 'undefined' && imgObj.width > w) {
							_w = w;
							_h = parseInt(_w * imgObj.height / imgObj.width);
						}
					}

					var imgStr = '<img src="' + imgObj.url + '" width="' + _w + '" height="' + _h + '" />';
					layer.open({
						type       : 1,
						title      : false,
						closeBtn   : 0,
						area       : _w,
						skin       : 'layui-layer-lan', //没有背景色
						shadeClose : true,
						content    : imgStr
					});
				}
			}
		});

		// reload
		$body.on('click', '.J_reload', function() {
			window.location.reload();
		});

		// print
		$body.on('click', '.J_print', function() {
			window.print();
		});

		/**
		 * 把当前表单的数据临时提交到指定的地址
		 * .J_submit     用法
		 * data-url     : 设置本表单请求的URL
		 * data-ajax    : true|false  设置是否进行ajax 请求
		 * data-confirm : 确认操作提交的提示信息
		 * data-method  : 提交方式
		 */
		$body.on('click', '.J_submit', function(e) {
			var request_url = $(this).attr('data-url');
			var $form = $(this).parents('form');
			if (!$form.length) {
				Util.splash({
					status : 'error',
					msg    : '您不在表单范围内， 请添加到表单范围内'
				});
				return false;
			}

			var old_url = $form.attr('action');
			if (!request_url) {
				request_url = old_url;
			}
			// confirm
			var str_confirm = $(this).attr('data-confirm');
			if (str_confirm === 'true') {
				str_confirm = '您确定删除此条目 ?';
			}
			if (str_confirm && !confirm(str_confirm)) return false;

			var data_ajax = $(this).attr('data-ajax');
			var data_method = $(this).attr('data-method') ? $(this).attr('data-method') : 'post';

			$form.attr('action', request_url);
			$form.attr('method', data_method);

			// 显示 layer 层
			var index = layer.load(0, {shade : false});
			var conf;
			if ((data_ajax === 'false')) {
				conf = Util.validateConfig({}, false);
				$form.validate(conf);
				$form.submit();
			} else {
				conf = Util.validateConfig({}, true);
				$form.validate(conf);
				var $btn = $(this);
				Util.buttonInteraction($btn, 5);
				$form.ajaxSubmit({
					success : function(data) {
						layer.close(index);
						Util.splash(data);
						Util.buttonInteraction($btn, data)
					}
				});
			}
			// 还原
			$form.attr('action', old_url);
			e.preventDefault();
		});

		/**
		 * 表单的验证提交
		 */
		$body.on('click', '.J_validate', function(element) {
			var $form = $(this).parents('form');
			if (!$form.length) {
				Util.splash({
					status  : 1,
					message : '没有 form 表单'
				});
				return;
			}

			// confirm
			var data_ajax = $form.attr('data-ajax');
			var conf;
			if ((data_ajax === 'false')) {
				conf = Util.validateConfig({}, false);
				$form.validate(conf);
				// ajax 禁用掉默认
				$(element).on('click', function(e) {
					e.preventDefault();
				})
			} else {
				conf = Util.validateConfig({}, true);
				$form.validate(conf);
			}
		});


		/**
		 * 禁用按钮
		 */
		$body.on('click', '.J_delay', function(e) {
			var $this = $(this);
			var tag = $this.prop("tagName").toLowerCase();
			if (tag == 'a' && !$this.data('delay')) {
				var _href = $(this).attr('href');
				$this.attr('href', 'javascript:void(0)').addClass('disabled').attr('data-delay', 'ing');
				setTimeout(function() {
					$this.attr('href', _href).removeClass('disabled').removeAttr('data-delay');
				}, 3000);
				e.preventDefault();
			}
			if (tag == 'button' && !$this.data('delay')) {
				$this.addClass('disabled');
				if ($(this).parents('form') && $this.prop('type') == 'submit') {
					$(this).parents('form').submit(function() {
						$this.prop('disabled', true);
					});
				}
				setTimeout(function() {
					$this.removeClass('disabled');
					$this.prop('disabled', false);
				}, 3000)
			}

		});

		/**
		 * 返回传输的内容, 并且将内容显示在弹窗中
		 */
		$(".J_info").each(function() {
			var $this = $(this);
			var data_url = $this.attr("data-url");
			var layer_id = "";
			var index = '';
			var common_opt = {
				type     : 1,
				area     : ['400px', 'auto'],
				tips     : [2, '#fff'],
				closeBtn : 0,
				shade    : 0,
				shift    : 5
			};
			$this.on("mouseover", function() {
				$.ajax({
					type    : 'get',
					url     : data_url,
					data    : {
						_token : Util.csrfToken()
					},
					success : function(data) {
						var com_content = data.content; //html内容
						var com_opt = $.extend({}, common_opt, {
							content : com_content,
							success : function(layer_obj) {
								layer_id = layer_obj.selector;
							}
						});
						index = layer.open(com_opt);
					},
					error   : function(XMLHttpRequest, textStatus, errorThrown) {
						alert(XMLHttpRequest.status);
						alert(XMLHttpRequest.readyState);
						alert(textStatus);
					}
				})
			}).on("mouseout", function() {
				var count = 0;
				$(layer_id).on('mouseover', function() {
					count = 1;
				}).on('mouseout', function() {
					count = 0;
				});
				$this.on('mouseover', function() {
					count = 1;
				});
				$body.on('mouseover', function() {
					if (count == 3) {
						clearInterval(t);
					}
				});
				var t = setInterval(function() {
					if (count == 0) {
						layer.close(index);
						count = 3;
					}
				}, 150);
			})
		});
	})


	$('body').on('keydown', function(event) {
		if (event.keyCode == 27) { // esc
			layer.closeAll();
		}
	});
})();
