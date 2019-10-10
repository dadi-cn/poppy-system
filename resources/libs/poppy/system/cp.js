/**
 * 后台控制面板
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2018 Sour Lemon Team
 */
(function() {

	$(function() {

		if (typeof moment !== 'undefined'){
			moment.locale('zh-cn')
		}

		var $body = $('body');

		$("[data-toggle='tooltip']").tooltip();

		//Initialization
		Waves.attach('.layui-btn', ['waves-light']);
		Waves.attach('.btn-flat', ['waves-effect']);
		Waves.attach('.chip', ['waves-effect']);
		Waves.attach('.view a .mask', ['waves-light']);
		Waves.attach('.waves-light', ['waves-light']);
		Waves.attach('.navbar-nav a:not(.navbar-brand), .nav-icons li a, .nav-tabs .nav-item:not(.dropdown)', ['waves-light']);
		Waves.attach('.pager li a', ['waves-light']);
		Waves.attach('.pagination .page-item .page-link', ['waves-effect']);
		Waves.init();

		// backend nav
		var $showCtr = $('#show-slide_out');
		var $hideCtr = $('#hide-slide_out');
		var $ele = $('#slide-out');
		$showCtr.on('click', function() {
			$ele.css({
				transform  : 'translateX(0)',
				transition : 'transform 0.5s ease-in-out'

			}).animate();
			$body.addClass('fixed-sn-force');
			$hideCtr.show();
			$showCtr.hide();
		});
		$hideCtr.on('click', function() {
			$ele.css({
				transform  : 'translateX(-105%)',
				transition : 'transform 0.5s ease-in-out'

			}).animate();
			$body.removeClass('fixed-sn-force');
			$hideCtr.hide();
			$showCtr.show();
		});


		layui.use(['form'], function() {
			layui.form.on('submit(ajax)', function(data) {
				var $this = $(data.elem);

				var request_url = $this.attr('data-url');
				var $form = $this.parents('form');
				if (!$form.length) {
					Util.splash({
						status : 1,
						msg    : '您不在表单范围内， 请添加到表单范围内'
					});
					return false;
				}

				var old_url = $form.attr('action');
				if (!request_url) {
					request_url = old_url;
				}
				// confirm
				var str_confirm = $this.attr('data-confirm');
				if (str_confirm === 'true') {
					str_confirm = '您确定删除此条目 ?';
				}
				if (str_confirm && !confirm(str_confirm)) return false;

				var data_ajax = $this.attr('data-ajax');
				var data_method = $this.attr('data-method') ? $this.attr('data-method') : 'post';

				$form.attr('action', request_url);
				$form.attr('method', data_method);

				// 显示 layer 层
				var index = layer.load(0, {shade : false});
				if ((data_ajax === 'false')) {
					$form.submit();
				} else {
					var $btn = $this;
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

				return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
			});
		});


		if ($.support.pjax) {
			$(document).on('submit', 'form[data-pjax]', function(event) {
				var container = $(this).attr('pjax-ctr');
				if (!container) {
					container = '#pjax-container'
				}
				$.pjax.submit(event, container, {
					fragment : container,
					timeout  : 3000,
				});
				event.preventDefault();
			});
			$(document).on('click', 'a[data-pjax], [data-pjax] a:not(.J_ignore)', function(event) {
				var container = $(this).closest('[pjax-ctr]');
				var ctr = container.attr('pjax-ctr');
				if (typeof ctr === 'undefined') {
					ctr = '#pjax-container'
				}

				if ($(ctr).length === 0) {
					Util.splash({
						status  : 1,
						message : '你的页面中没有 Pjax 容器' + ctr + ',请添加, 否则无法进行页面请求'
					});
					return false;
				}

				$.pjax.click(event, {
					container : ctr,
					fragment  : ctr,
					timeout   : 3000
				})
			});
			$(document).on('pjax:send', function() {
				layer.load(3)
			});
			$(document).on('pjax:complete', function() {
				$('.J_tooltip').tooltip();
				layer.closeAll();
				layui.form.render();
			});
		}
	})
})();