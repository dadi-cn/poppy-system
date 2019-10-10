/**
 * Poppy 的核心函数类 (全局)
 */
if (typeof jQuery === 'undefined') {
	alert('You need import jquery before poppy global util');
}

if (typeof Util !== 'object') {
	Util = {};
}


(function($) {
	$.fn.plugin_validate_tip = function(message, second, option) {
		if (typeof second === 'undefined') {
			second = 3;
		}
		// $(".tip-yellowsimple").remove();
		try {
			$(this).poshytip({
				className    : 'tip-yellowsimple',
				content      : message,
				timeOnScreen : second * 1000,
				showOn       : 'none',
				alignTo      : 'target',
				alignX       : 'inner-left',
				offsetX      : 0,
				offsetY      : 5
			}).poshytip("show");
			$(this).addClass('layui-form-danger');
			// $(this).focus();
			// 注意，要结合jquery.validation必须放在后面
		} catch (e) {
			$(this).on('blur', function() {
				alert(message);
			});
		}
	};

	if (typeof $.validator !== 'undefined') {

		$.validator.addMethod("mobile", function(phone_number, element) {
			phone_number = phone_number.replace(/\(|\)|\s+|-/g, "");
			return this.optional(element) || phone_number.length > 9 &&
				phone_number.match(/^1[3|4|5|8|7|9][0-9]\d{4,8}$/);
		}, "Please specify a valid mobile number");

		$.validator.addMethod("qq", function(qq_number, element) {
			qq_number = qq_number.replace(/\(|\)|\s+|-/g, "");
			return this.optional(element) || qq_number.length > 4 &&
				qq_number.match(/^[1-9]\d{3,10}$/);
		}, "Please specify a valid qq number");

		// 中国电话号码的验证
		$.validator.addMethod("phoneZh", function(value, element) {
			return this.optional(element) || /^(([0\+]\d{2,3}-?)?(0\d{2,3})-?)?(\d{7,8})(-(\d{3,}))?$/.test(value);
		}, "Please specify a valid phone number.");

		// 中国电话号码和手机的验证
		$.validator.addMethod("phoneAmobile", function(value, element) {
			var phone_number = value.replace(/\(|\)|\s+|-/g, "");
			return (this.optional(element) || /^(([0\+]\d{2,3}-?)?(0\d{2,3})-?)?(\d{7,8})(-(\d{3,}))?$/.test(value))
				||
				(this.optional(element) || phone_number.length > 9 &&
					phone_number.match(/^1[3|4|5|8][0-9]\d{4,8}$/));
		}, "Please specify a valid phone number.");

		// 中文身份证验证
		$.validator.addMethod("chId", function(chId, element) {
			var iW = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
			var iSum = 0;
			var iC, iVal;
			for (var i = 0; i < 17; i++) {
				iC = chId.charAt(i);
				iVal = parseInt(iC, 10);
				iSum += iVal * iW[i];
			}
			var iJYM = iSum % 11;
			var sJYM = '';
			if (iJYM === 0) sJYM = "1";
			else if (iJYM === 1) sJYM = "0";
			else if (iJYM === 2) sJYM = "x";
			else if (iJYM === 3) sJYM = "9";
			else if (iJYM === 4) sJYM = "8";
			else if (iJYM === 5) sJYM = "7";
			else if (iJYM === 6) sJYM = "6";
			else if (iJYM === 7) sJYM = "5";
			else if (iJYM === 8) sJYM = "4";
			else if (iJYM === 9) sJYM = "3";
			else if (iJYM === 10) sJYM = "2";
			var cCheck = chId.charAt(17).toLowerCase();
			return sJYM && cCheck == sJYM;
		}, "Please specify a valid chinese id");

		// 不允许含有空格
		$.validator.addMethod("noSpace", function(value, element) {
			return !/\s+/.test(value);
		}, "Please do not insert space");

		/* 小数验证，小数点位数按照max参数的小数点位数进行判断
		 * 不能为空、只能输入数字 */
		$.validator.addMethod("decimal", function(value, element, params) {
			if (!value) {
				return true;
			}
			if (isNaN(params[0])) {
				return false;
			}
			if (isNaN(params[1])) {
				return false;
			}
			if (isNaN(params[2])) {
				return false;
			}
			if (isNaN(value)) {
				return false;
			}
			if (typeof (value) == undefined || value == "") {
				return false;
			}
			var min = Number(params[0]);
			var max = Number(params[1]);
			var testVal = Number(value);
			if (typeof (params[2]) == undefined || params[2] == 0) {
				var regX = /^\d+$/;
			} else {
				var regxStr = "^\\d+(\\.\\d{1," + params[2] + "})?$";
				var regX = new RegExp(regxStr);
			}
			return this.optional(element) || (regX.test(value) && testVal >= min && testVal <= max);
		}, $.validator.format("请正确输入在{0}到{1}之间，最多只保留小数点后{2}的数值"));

		$.extend($.validator.messages, {
			required     : "必须填写",
			remote       : "请修正此栏位",
			email        : "请输入有效的电子邮件",
			qq           : '请输入正确的QQ号',
			mobile       : '请输入正确的手机号',
			phoneZh      : '请输入正确的固定电话号码',
			phoneAmobile : '请输入正确的固话或者手机号',
			url          : "请输入有效的网址",
			date         : "请输入有效的日期",
			dateISO      : "请输入有效的日期 (YYYY-MM-DD)",
			number       : "请输入正确的数字",
			digits       : "只可输入数字",
			creditcard   : "请输入有效的信用卡号码",
			equalTo      : "你的输入不相同",
			extension    : "请输入有效的后缀",
			maxlength    : $.validator.format("最多 {0} 个字"),
			minlength    : $.validator.format("最少 {0} 个字"),
			eqlength     : $.validator.format("请输入 {0} 长度的字符!"),
			rangelength  : $.validator.format("请输入长度为 {0} 至 {1} 之间的字串"),
			range        : $.validator.format("请输入 {0} 至 {1} 之间的数值"),
			max          : $.validator.format("请输入不大于 {0} 的数值"),
			min          : $.validator.format("请输入不小于 {0} 的数值"),
			ipv4         : '请输入正确的IP地址',
			chId         : '请输入正确的身份证信息',
			noSpace      : '请不要在此输入空格',
			alphanumeric : '请输入字母, 数字, 下划线的组合!',
			decimal      : '请正确输入在{0}到{1}之间，最多只保留小数点后{2}的数值'
		});
	}
})(jQuery);


(function() {
	'use strict';

	/**
	 * 点击加入收藏
	 * @param id
	 */
	Util.addFav = function(id) {
		$(id).on('click', function() {
			if (document.all) {
				try {
					window.external.addFavorite(window.location.href, document.title);
				} catch (e) {
					alert("加入收藏失败，请使用Ctrl+D进行添加");
				}
			} else if (window.sidebar) {
				window.sidebar.addPanel(document.title, window.location.href, "");
			} else {
				alert("加入收藏失败，请使用Ctrl+D进行添加");
			}
		})
	};


	/**
	 * 返回浏览器的版本和ie的判定
	 * @returns {{version: *, safari: boolean, opera: boolean, msie: boolean, mozilla: boolean, is_ie8: boolean, is_ie9: boolean, is_ie10: boolean, is_rtl: boolean}}
	 */
	Util.browser = function() {
		var userAgent = navigator.userAgent.toLowerCase();
		return {
			version   : (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [0, '0'])[1],
			safari    : /webkit/.test(userAgent),
			opera     : /opera/.test(userAgent),
			msie      : /msie/.test(userAgent) && !/opera/.test(userAgent),
			mozilla   : /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent),
			is_ie8    : !!userAgent.match(/msie 8.0/),
			is_ie9    : !!userAgent.match(/msie 9.0/),
			is_ie10   : !!userAgent.match(/msie 10.0/),
			is_wechat : !!userAgent.match(/micromessenger/),
			is_rtl    : $('body').css('direction') === 'rtl'
		}
	};

	/*
	 * 提示信息
	 * @params word  String 提示信息
	 * */
	Util.splash = function(resp, append_callback) {
		var obj_resp = Util.toJson(resp);
		var obj_data = {
			callback : '',
			show     : 'tip',
			time     : 0
		};
		var obj_init = {
			message : 'No Message Send By Server!',
			status  : 1
		};

		obj_resp = $.extend(obj_init, obj_resp);
		if (typeof obj_resp.data !== 'undefined') {
			obj_data = $.extend(obj_data, obj_resp.data);
		}
		if (obj_data.show === 'tip') {
			obj_data.time = parseInt(obj_data.time) ? parseInt(obj_data.time) : 0;
			var jump_time;
			if (!obj_data.time && obj_data.location) {
				jump_time = 800;
			}
			if (!obj_data.time && obj_data.reload) {
				jump_time = 800;
			}
			if (!obj_data.time && obj_data.reload_opener) {
				jump_time = 800;
			}
			if (typeof window.mobile !== 'undefined') {
				layer.msg(obj_resp.message, {
					time : 3000
				})
			} else {
				setTimeout(function() {
					toastr.options = {
						closeButton   : true,
						progressBar   : true,
						showMethod    : 'fadeIn',
						timeOut       : 4000,
						positionClass : "toast-top-center",
					};
					if (obj_resp.status === 0) {
						toastr.success(obj_resp.message);
					} else {
						toastr.error(obj_resp.message);
					}
				}, jump_time);
			}
		}

		if (obj_data.show === 'dialog') {
			delete obj_resp.show;
			var conf = {};
			var title = !conf.hasOwnProperty('title') ? conf.msg : conf.title;
			var content = !conf.hasOwnProperty('content') ? conf.msg : conf.content;
			layer.open({
				title   : title,
				content : content
			});
			return false;
		}

		if (obj_data.show === 'callback' || obj_data.callback) {
			var func = obj_data.callback;
			setTimeout(function() {
				eval(func + ";");
			}, obj_data.time);
		}

		if (obj_data.reload) {
			var $winPjax = window.$('form[data-pjax]');
			if ($winPjax.length) {
				$winPjax.submit();
			} else {
				setTimeout(function() {
					if (Util.browser().is_wechat) {
						window.location.search = '?v='+Date.now();
					} else {
						window.location.reload()
					}
				}, obj_data.time);
				return;
			}
		}
		if (obj_data.top_reload) {
			if (typeof top.window.layui !== 'undefined' && typeof top.window.layui.admin !== 'undefined') {
				top.window.layui.admin.events.refresh();
			} else {
				if (typeof top.window.$ != 'undefined') {
					var $topPjax = top.window.$('form[data-pjax]');
					if ($topPjax.length) {
						$topPjax.submit();
					} else {
						setTimeout(function() {
							top.window.location.reload()
						}, obj_data.time);
					}
				} else {
					setTimeout(function() {
						top.window.location.reload()
					}, obj_data.time);
				}
			}
		}

		if (obj_data.location) {
			setTimeout(function() {
				window.location.href = obj_data.location;
			}, obj_data.time);
		}

		if (obj_data.top_location) {
			setTimeout(function() {
				top.window.location.href = obj_data.top_location;
			}, obj_data.time);
		}

		if (obj_data.reload_opener) {
			setTimeout(function() {
				if (typeof top.layui !== 'undefined' && typeof top.layui.admin !== 'undefined') {
					top.layui.admin.refresh();
				} else {
					top.location.reload();
				}
			}, obj_data.time);
		}

		if (obj_data.iframe_close) {
			setTimeout(function() {
				var opener = Util.opener(obj_data.iframe_close);
				opener.iframe.close();
			}, obj_data.time);
		}

		if (obj_data.captcha_reload) {
			$('.J_captcha').trigger('click');
		}

		if (obj_data.pjax) {
			var $topPjax = top.window.$('form[data-pjax]');
			if ($topPjax.length) {
				$topPjax.submit();
			} else {
				$('form[data-pjax]').submit();
			}
		}

		if (obj_data._update) {
			var tagName = $(obj_data._update)[0].localName;
			if (tagName === 'textarea' || tagName === 'input') {
				$(obj_data._update).val(obj_data._content)
			} else {
				$(obj_data._update).html(obj_data._content)
			}
		}

		if (typeof append_callback === 'function') {
			append_callback(obj_resp);
		}
	};
	/**
	 * 字串转 json
	 * @param resp
	 * @returns {*}
	 */
	Util.toJson = function(resp) {
		var objResp;
		if (typeof resp === 'object') {
			objResp = resp;
		} else {
			if ($.trim(resp) === '') {
				objResp = {};
			} else {
				objResp = $.parseJSON(resp);
			}
		}
		return objResp;
	};

	/**
	 * 获取 openner
	 * @param workspace
	 * @returns {*}
	 */
	Util.opener = function(workspace) {
		var opener = top.frames[workspace];
		if (typeof opener === 'undefined') {
			opener = top;
		}
		return opener;
	};

	/**
	 * 按钮交互
	 * @param btn_selector
	 * @param data
	 * @param error_submit
	 */
	Util.buttonInteraction = function(btn_selector, data, error_submit) {
		var objData;
		if (typeof data == 'undefined' || !isNaN(parseInt(data))) {
			$(btn_selector).attr('disabled', true);
			if (!isNaN(parseInt(data))) {
				var time = parseInt(data);
				setTimeout(function() {
					$(btn_selector).attr('disabled', false);
				}, time * 1000);
			}
		}
		objData = Util.toJson(data);
		if (objData.status == 'error') {
			$(btn_selector).attr('disabled', false);
			if (typeof error_submit != 'undefined') {
				$(btn_selector).html(error_submit);
			}
		}
	};


	/**
	 * 事件请求, 使用post 方法
	 * @param $this
	 * @param splash_func
	 * @returns {boolean}
	 */
	Util.requestEvent = function($this, splash_func) {
		// confirm
		var str_confirm = $this.attr('data-confirm');
		if (str_confirm === 'true') {
			str_confirm = '您确定删除此条目 ?';
		}
		if (str_confirm) {
			if (!confirm(str_confirm)) {
				layer.closeAll();
				return false;
			}
		}
		var append = $this.attr('data-append');
		var data = Util.appendToObj(append);

		var condition_str = $this.attr('data-condition');
		var condition = Util.conditionToObj(condition_str);
		for (var i in data) {
			if (condition.hasOwnProperty(i) && !data.hasOwnProperty(i)) {
				splash_func({
					'status'  : 1,
					'message' : condition[i]
				});
				return false;
			}
		}

		var update = $this.attr('data-update');
		if (update) {
			data._update = update;
		}

		// do request
		var href = $this.attr('href');
		if (!href) {
			href = $this.attr('data-url');
		}
		data._token = Util.csrfToken();
		console.log(data);
		$.post(href, data, splash_func);
	};

	/**
	 * 获取页面中的 csrf token
	 * @returns {*|jQuery}
	 */
	Util.csrfToken = function() {
		return $('meta[name="csrf-token"]').attr('content');
	};


	/**
	 * 追加元素到对象
	 * @param append
	 * @returns {{}}
	 */
	Util.appendToObj = function(append) {
		var data = {};
		if (append) {
			var appends = [append];
			if (append.indexOf(',') >= 0) {
				appends = append.split(',');
			}
			for (var i in appends) {
				var item = appends[i];
				var re = /(.*)\((.*)\)/;
				var m;

				if ((m = re.exec(item)) !== null) {
					if (m.index === re.lastIndex) {
						re.lastIndex++;
					}
				}

				if (m[1].indexOf('checked') >= 0 && m[1].indexOf('radio') < 0) {
					var id_array = [];
					$(m[1]).each(function() {
						id_array.push($(this).val());//向数组中添加元素
					});
					data[m[2]] = id_array;//将数组元素连接起来以构建一个字符串
				} else {
					data[m[2]] = $(m[1]).val();
				}

			}
		}
		return data;
	};


	/**
	 * 条件转换
	 * @param append
	 * @returns {{}}
	 */
	Util.conditionToObj = function(append) {
		var data = {};
		if (append) {
			var appends = append.split(',');
			for (var i in appends) {
				var item = appends[i];
				var re = /(.*):(.*)/;
				var m;
				if ((m = re.exec(item)) !== null) {
					if (m.index === re.lastIndex) {
						re.lastIndex++;
					}
					data[m[1]] = m[2];
				}
			}
		}
		return data;
	};

	/**
	 * 对象转换成url地址
	 * @param obj
	 * @param url
	 * @returns {*}
	 */
	Util.objToUrl = function(obj, url) {
		var str = "";
		for (var key in obj) {
			if (str != "") {
				str += "&";
			}
			str += key + "=" + obj[key];
		}
		if (typeof url != 'undefined') {
			return url.indexOf('?') >= 0 ? url + '&' + str : url + '?' + str;
		} else {
			return str;
		}
	};

	/**
	 * 预览图像地址
	 * @param imgSrc
	 * @param w
	 * @returns {boolean}
	 */
	Util.imagePopupShow = function(imgSrc, w) {
		if (!imgSrc) {
			Util.splash({
				status  : 1,
				message : '没有图像文件',
			});
			return false;
		}
		Util.imageSize(imgSrc, _popup_show);

		/**
		 * imgObj.width   imgObj.height  imgObj.url
		 * @param imgObj
		 * @private
		 */
		function _popup_show(imgObj) {
			var _w = imgObj.width;
			var _h = imgObj.height;
			if (typeof w != 'undefined' && imgObj.width > w) {
				_w = w;
				_h = parseInt(_w * imgObj.height / imgObj.width);
			}
			var imgStr = '<img src="' + imgObj.url + '" width="' + _w + '" height="' + _h + '" />';
			layer.open({
				title   : '图片预览',
				content : imgStr,
				area    : [(_w + 40) + 'px', (_h + 80) + 'px'],
			});
		}
	};


	/**
	 * 计算图片的大小
	 * @param sUrl
	 * @param fCallback
	 */
	Util.imageSize = function(sUrl, fCallback) {
		var img = new Image();
		img.src = sUrl + '?t=' + Math.random();    //IE下，ajax会缓存，导致onreadystatechange函数没有被触发，所以需要加一个随机数
		if (Util.browser().msie) {
			img.onreadystatechange = function() {
				if (this.readyState == "loaded" || this.readyState == "complete") {
					fCallback({width : img.width, height : img.height, url : sUrl});
				}
			};
		} else if (Util.browser().mozilla || Util.browser().safari || Util.browser().opera) {
			img.onload = function() {
				fCallback({width : img.width, height : img.height, url : sUrl});
			};
		} else {
			fCallback({width : img.width, height : img.height, url : sUrl});
		}
	};

	/**
	 * 通过 post 的方法异步读取数据
	 * @param targetPhp
	 * @param queryString
	 * @param success
	 * @param method
	 */
	Util.makeRequest = function(targetPhp, queryString, success, method) {
		if (typeof queryString === 'string') {
			queryString += queryString.indexOf('&') < 0
				? '_token=' + Util.csrfToken()
				: '&_token=' + Util.csrfToken();
		}
		if (typeof queryString === 'object') {
			queryString['_token'] = Util.csrfToken();
		}
		if (typeof queryString === 'undefined') {
			queryString = {
				'_token' : Util.csrfToken()
			}
		}
		if (typeof success === 'undefined') {
			success = Util.splash;
		}
		if (typeof method === 'undefined') {
			method = 'post';
		}
		$.ajax({
			async   : false,
			cache   : false,
			type    : method,
			url     : targetPhp,
			data    : queryString,
			success : function(data) {
				var obj_data = Util.toJson(data);
				success(obj_data);
			}
		});
	};

	/**
	 * 验证配置
	 * @param rules
	 * @param is_ajax
	 */
	Util.validateConfig = function(rules, is_ajax) {
		var config = {
			ignore : '.ignore'
		};
		if (is_ajax) {
			config.submitHandler = function(form) {
				$(form).ajaxSubmit({
					success : Util.splash
				});
			};
		}
		config.highlight = function(element) {
			$(element).closest('.form-group').addClass('has-error');
		};
		config.unhighlight = function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		};
		config.errorElement = 'span';
		config.errorClass = 'help-block';
		config.errorPlacement = function(error, element) {
			$(element).plugin_validate_tip(error.text());
		};
		return $.extend(config, rules);
	};

	/**
	 * 获取当前视窗的大小
	 * To get the correct viewport width
	 * based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
	 * @returns {{width: *, height: *}}
	 */
	Util.getViewport = function() {
		var e = window,
			a = 'inner';
		if (!('innerWidth' in window)) {
			a = 'client';
			e = document.documentElement || document.body;
		}

		return {
			width  : e[a + 'Width'],
			height : e[a + 'Height']
		};
	};


	/**
	 * 检测给定的字串是否是 Url
	 * @param str
	 * @returns {boolean}
	 */
	Util.isUrl = function(str) {
		var pattern = new RegExp("^(https?:\\/\\/)?" + // protocol
			"((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
			"((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
			"(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
			"(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
			"(\\#[-a-z\\d_]*)?$", 'i'); // fragment locater
		return pattern.test(str);
	};

	/**
	 * 判定是否是邮箱
	 * @param str
	 * @returns {boolean}
	 */
	Util.isEmail = function(str) {
		var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
		return reg.test(str);
	};

	/**
	 * 判定是否为手机号码
	 * @param str
	 * @returns {boolean|Array|{index: number, input: string}}
	 */
	Util.isMobile = function(str) {
		var phone_number = str.replace(/\(|\)|\s+|-/g, "");
		return phone_number.length > 10 && phone_number.match(/^1[3|4|5|6|8|7|9][0-9]\d{4,8}$/);
	};

	/**
	 * 按钮倒计时工具
	 * @param btn_selector
	 * @param str
	 * @param time
	 * @param end_str
	 */
	Util.countdown = function(btn_selector, str, time, end_str) {
		var count = time;
		var handlerCountdown;
		var $btn = $(btn_selector);
		var displayStr = typeof end_str != 'undefined' ? end_str : $btn.text();

		handlerCountdown = setInterval(_countdown, 1000);
		$btn.attr("disabled", true);

		function _countdown() {
			var count_str = str.replace(/\{time\}/, count);
			$btn.text(count_str);
			if (count == 0) {
				$btn.text(displayStr).removeAttr("disabled");
				clearInterval(handlerCountdown);
			}
			count--;
		}
	};

	/**
	 *
	 * @param game_id
	 * @param server_ctr
	 * @param server_key
	 * @param opts
	 */
	Util.serverHtml = function(game_id, server_ctr, server_key, opts) {
		$(function() {
			var $game_id = $('#' + game_id);
			$game_id.on('change', function() {
				get_server($(this).val());
			});
			get_server($game_id.val());
		});

		function get_server(game_id) {
			if (!game_id) return;
			$.get(lemon.support_url.game_server_html, {
				game_id    : game_id,
				server_key : server_key,
				options    : opts
			}, function(data) {
				$('#' + server_ctr).html(data);
			})
		}
	};

	Util.typeHtml = function(game_id, type_ctr, type_key, opts) {
		$(function() {
			var $game_id = $('#' + game_id);
			$game_id.on('change', function() {
				get_type($(this).val());
			});
			get_type($game_id.val());
		});

		function get_type(game_id) {
			if (!game_id) return;
			$.get(lemon.support_url.game_type_html, {
				game_id  : game_id,
				type_key : type_key,
				options  : opts
			}, function(data) {
				$('#' + type_ctr).html(data);
			})
		}
	};

	/**
	 * 生成随机字符
	 * @param length
	 * @returns {string}
	 */
	Util.random = function(length) {
		if (typeof length == 'undefined' || parseInt(length) == 0) {
			length = 18;
		}
		var chars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
		var str = '';
		for (var i = 0; i < length; i++) {
			str += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		return str;
	};

	/**
	 * 方便添加维护类
	 * @returns {{hasClass: *, addClass: *, removeClass: *, toggleClass: toggleClass, has: *, add: *, remove: *, toggle: toggleClass}}
	 */
	Util.classie = function() {
		function classReg(className) {
			return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
		}

		// classList support for class management
		// altho to be fair, the api sucks because it won't accept multiple classes at once
		var hasClass, addClass, removeClass;

		if ('classList' in document.documentElement) {
			hasClass = function(elem, c) {
				return elem.classList.contains(c);
			};
			addClass = function(elem, c) {
				elem.classList.add(c);
			};
			removeClass = function(elem, c) {
				elem.classList.remove(c);
			};
		} else {
			hasClass = function(elem, c) {
				return classReg(c).test(elem.className);
			};
			addClass = function(elem, c) {
				if (!hasClass(elem, c)) {
					elem.className = elem.className + ' ' + c;
				}
			};
			removeClass = function(elem, c) {
				elem.className = elem.className.replace(classReg(c), ' ');
			};
		}

		function toggleClass(elem, c) {
			var fn = hasClass(elem, c) ? removeClass : addClass;
			fn(elem, c);
		}

		return {
			// full names
			hasClass    : hasClass,
			addClass    : addClass,
			removeClass : removeClass,
			toggleClass : toggleClass,
			// short names
			has         : hasClass,
			add         : addClass,
			remove      : removeClass,
			toggle      : toggleClass
		};
	};


	/**
	 * 计算对象的长度
	 * @param obj
	 * @returns {number}
	 */
	Util.objSize = function(obj) {
		var count = 0;

		if (typeof obj == "object") {

			if (Object.keys) {
				count = Object.keys(obj).length;
			} else if (window._) {
				count = _.keys(obj).length;
			} else if (window.$) {
				count = $.map(obj, function() {
					return 1;
				}).length;
			} else {
				for (var key in obj) if (obj.hasOwnProperty(key)) count++;
			}

		}

		return count;
	};

	/**
	 * 重新载入当前页面
	 */
	Util.refresh = function() {
		top.window.location.reload();
	};

	Util.opener = function(workspace) {
		var opener = top.frames[workspace];
		if (typeof opener == 'undefined') {
			opener = top;
		}
		return opener;
	};

	/**
	 * 执行一次动画
	 * @param selector
	 * @param animation_name
	 */
	Util.animate = function(selector, animation_name) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		$(selector).addClass('animated ' + animation_name).one(animationEnd, function() {
			$(this).removeClass('animated ' + animation_name);
		});
	};

	/**
	 * 全屏
	 * @param ele
	 */
	Util.fullScreen = function(ele) {
		var element;
		if (typeof ele == 'undefined') {
			element = document.documentElement;
		} else {
			element = document.getElementById(ele);
		}
		if (element.requestFullscreen) {
			element.requestFullscreen();
		} else if (element.mozRequestFullScreen) {
			element.mozRequestFullScreen();
		} else if (element.webkitRequestFullscreen) {
			element.webkitRequestFullscreen();
		} else if (element.msRequestFullscreen) {
			element.msRequestFullscreen();
		}
	};

	/**
	 * 退出全屏
	 */
	Util.exitFullScreen = function() {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
		}
	};


	/**
	 * 检查浏览器是否支持 local 存储
	 * @returns {boolean}
	 */
	Util.localStorageSupport = function() {
		return (('localStorage' in window) && window['localStorage'] !== null)
	};

	/**
	 * 获取 Url 参数
	 * @param paramName
	 * @returns {string}
	 */
	Util.getUrlParameter = function(paramName) {
		var searchString = window.location.search.substring(1),
			i, val, params = searchString.split("&");

		for (i = 0; i < params.length; i++) {
			val = params[i].split("=");
			if (val[0] == paramName) {
				return unescape(val[1]);
			}
		}
		return '';
	};

	/**
	 * 获取当前视窗的大小
	 * To get the correct viewport width
	 * based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
	 * @returns {{width: *, height: *}}
	 */
	Util.getViewport = function() {
		var e = window,
			a = 'inner';
		if (!('innerWidth' in window)) {
			a = 'client';
			e = document.documentElement || document.body;
		}

		return {
			width  : e[a + 'Width'],
			height : e[a + 'Height']
		};
	};

	/**
	 * 是否是触摸设备
	 * check for device touch support
	 * @returns {boolean}
	 */
	Util.isTouchDevice = function() {
		try {
			document.createEvent("TouchEvent");
			return true;
		} catch (e) {
			return false;
		}
	};


	/**
	 * 获取唯一ID
	 * @param prefix
	 * @returns {string}
	 */
	Util.getUniqueId = function(prefix) {
		var _pre = (typeof prefix == 'undefined') ? 'prefix_' : prefix;
		return _pre + Math.floor(Math.random() * (new Date()).getTime());
	};
})();

/**
 * 根据参数名获取对应的url参数
 * @param {string} name 要取的值key
 * @returns {string}
 */
function getQueryString(name) {
	let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	let r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]);
	return null;
}
