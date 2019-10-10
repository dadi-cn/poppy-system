<?php namespace Poppy\System\Http\Request\ApiV1\Web;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Poppy\Framework\Classes\Resp;
use Site\Action\BeHomeSetting;

/**
 * 系统信息控制
 */
class CoreController extends WebApiController
{
	use ThrottlesLogins;

	/**
	 * @api                    {post} api_v1/system/core/info 系统信息
	 * @apiVersion             1.0.0
	 * @apiName                SystemInfo
	 * @apiGroup               System
	 * @apiSuccess {object}   login                               |系统设置-第三方登录
	 * @apiSuccess {string}   login.wechat_mobile                 |系统设置-第三方登录-手机微信登录是否开启
	 * @apiSuccess {string}   login.qq_mobile                     |系统设置-第三方登录-手机qq登录是否开启
	 * @apiSuccess {object}   notice                              |其他-App设定-Im设置
	 * @apiSuccess {string}   notice.system_account               |其他-App设定-Im设置-系统通知账号
	 * @apiSuccess {string}   notice.notice_account               |官方公告账号
	 * @apiSuccess {string}   notice.order_account                |其他-App设定-Im设置-订单通知账号
	 * @apiSuccess {string}   notice.moments_account              |其他-App设定-Im设置-动态通知账号
	 * @apiSuccess {string}   online_time_expire                  |
	 * @apiSuccess {int}      play_auto_delete                    |订单配置-陪玩单-最大匹配时间(默认：5分钟)
	 * @apiSuccess {int}      cancel_order_time                   |系统配置-订单配置-取消订单-取消订单时间限制(分钟)
	 * @apiSuccess {int}      match_request_time                  |
	 * @apiSuccess {object}   help                                |系统配置-网站配置-帮助设置
	 * @apiSuccess {string}   help.qq                             |系统配置-网站配置-帮助设置-客服QQ
	 * @apiSuccess {string}   android_version                     |
	 * @apiSuccess {array}    act_popup                           |
	 * @apiSuccess {object}   boot_picture                        |
	 * @apiSuccess {string}   boot_picture.android                |
	 * @apiSuccess {string}   boot_picture.ios                    |
	 * @apiSuccess {int}      boot_picture.time                   |
	 * @apiSuccess {object}   girl_check                          |
	 * @apiSuccess {string}   girl_check.qq                       |
	 * @apiSuccess {string}   girl_check.android_key              |
	 * @apiSuccess {string}   girl_check.ios_key                  |
	 * @apiSuccess {object}   my_bottom_banner                    |
	 * @apiSuccess {string}   my_bottom_banner.title              |
	 * @apiSuccess {string}   my_bottom_banner.is_open            |
	 * @apiSuccess {string}   my_bottom_banner.picture            |
	 * @apiSuccess {string}   my_bottom_banner.return_url         |
	 * @apiSuccess {object[]} illegal_type                        |
	 * @apiSuccess {object}   vip_privilege_message               |
	 * @apiSuccess {string}   vip_privilege_message.visit_history |
	 * @apiSuccess {string}   vip_privilege_message.anonymous     |
	 * @apiSuccess {string}   vip_privilege_message.invisible     |
	 * @apiSuccess {string}   vip_privilege_message.barrage       |
	 * @apiSuccess {string}   vip_privilege_message.kefu          |
	 * @apiSuccess {string}   vip_privilege_message.discount      |
	 * @apiSuccess {string}   vip_privilege_message.gif_avatar    |
	 * @apiSuccess {string}   vip_privilege_message.rainbow       |
	 * @apiSuccess {string}   vip_privilege_message.unique_id     |
	 * @apiSuccess {object[]} picture                             |
	 * @apiSuccess {string}   picture.picture                     |
	 * @apiSuccess {string}   picture.return_url                  |
	 * @apiSuccess {string}   picture.title                       |
	 * @apiSuccess {string}   picture.action                      |
	 * @apiSuccess {object}   pay                                 |支付配置
	 * @apiSuccess {string}   pay.ali_h5                          |
	 * @apiSuccess {string}   pay.alipay_mobile                   |支付宝支付是否开启
	 * @apiSuccess {string}   pay.wxpay_mobile                    |微信支付是否开启
	 * @apiSuccess {string}   pay.wx_h5                           |
	 * @apiSuccess {string}   pay.weapp                           |
	 * @apiSuccess {string}   min_cash_amount                     |
	 * @apiSuccess {int}      car_invite_time                     |
	 * @apiSuccess {object[]} post_illegal_type                   |系统配置-圈子配置-举报设置-帖子举报
	 *
	 * @apiSuccessExample      返回
	 *
	 * {
	 *     "login":{
	 *         "wechat_mobile":true,
	 *         "qq_mobile":true
	 *     },
	 *     "notice":{
	 *         "system_account":"ddboss",
	 *         "notice_account":"idailian",
	 *         "order_account":"liexiang",
	 *         "moments_account":"idailian"
	 *     },
	 *     "online_time_expire":"15",
	 *     "play_auto_delete":2,
	 *     "cancel_order_time":2,
	 *     "match_request_time":15,
	 *     "help":{
	 *         "qq":"3030705530"
	 *     },
	 *     "android_version":"2.3.0",
	 *     "act_popup":[
	 *
	 *     ],
	 *     "boot_picture":{
	 *         "android":"",
	 *         "ios":"",
	 *         "time":3
	 *     },
	 *     "girl_check":{
	 *         "qq":"799855112",
	 *         "android_key":"cl6WAa9azOEqsJNzFL1L2C-ZJupZzjG5",
	 *         "ios_key":"94b6d09f63a32ce29f215a0460e8554d23cc5658a1d49c2739517080dc093253"
	 *     },
	 *     "my_bottom_banner":{
	 *         "title":"推广分享",
	 *         "is_open":"Y",
	 *         "picture":"https://oss-test.iliexiang.com/dev/default/201905/16/09/3339emQU4iIq.png",
	 *         "return_url":"http://t.play.iliexiang.com/api_v2/user/spread/invite"
	 *     },
	 *     "illegal_type":[
	 *         "反动言论",
	 *         "侵权侮辱攻击",
	 *         "诈骗",
	 *         "其他"
	 *     ],
	 *     "vip_privilege_message":{
	 *         "visit_history":"级别不够",
	 *         "anonymous":"级别不够",
	 *         "invisible":"级别不够",
	 *         "barrage":"级别不够",
	 *         "kefu":"级别不够",
	 *         "discount":"级别不够",
	 *         "gif_avatar":"需满足VIPX等级后才可上传动态头像",
	 *         "rainbow":"级别不够",
	 *         "unique_id":"级别不够"
	 *     },
	 *     "picture":[
	 *         {
	 *             "picture":"https://oss-test.iliexiang.com/dev/default/201902/16/15/4704Ev1mlOEL.png",
	 *             "is_open":true,
	 *             "return_url":"http://t.play.iliexiang.com/api_v1/site/activity/play_with",
	 *             "title":"娱乐陪玩攻略-新手指南",
	 *             "action":"h5_link"
	 *         }
	 *     ],
	 *     "pay":{
	 *         "ali_h5":true,
	 *         "alipay_mobile":true,
	 *         "wxpay_mobile":true,
	 *         "wx_h5":true,
	 *         "weapp":false
	 *     },
	 *     "min_cash_amount":"0.01",
	 *     "car_invite_time":10,
	 *     "post_illegal_type":[
	 *         "反动色情等国家禁止言论",
	 *         "侵权",
	 *         "侮辱攻击",
	 *         "虚假抽奖",
	 *         "诈骗",
	 *         "其他"
	 *     ]
	 * }
	 */
	public function info()
	{
		$system['login']  = [
			'wechat_mobile' => (bool) sys_setting('system::login_wechat.mobile_is_open'),
			'qq_mobile'     => (bool) sys_setting('system::login_qq.mobile_is_open'),
		];
		$system['notice'] = [
			'system_account'  => sys_setting('system::im_notice.system_account') ?? '',
			'notice_account'  => sys_setting('system::im_notice.notice_account') ?? '',
			'order_account'   => sys_setting('system::im_notice.order_account') ?? '',
			'moments_account' => sys_setting('system::im_notice.moments_account') ?? '',
		];

		$system['online_time_expire'] = sys_setting('site::app.online_time_expire') ?: 15;
		$system['play_auto_delete']   = (int) (sys_setting('order::play.auto_delete') ?? 5);

		//取消订单时间间隔（分钟）
		$system['cancel_order_time'] = (int) sys_setting('order::cancel_order.time') ?? 0;

		// @todo 后台设置
		$system['match_request_time'] = BeHomeSetting::getCacheMathNumAndPicture()['time'] ?? 5;

		$hook   = sys_hook('system.api_info');
		$system = array_merge($system, $hook);

		return Resp::web(Resp::SUCCESS, '获取系统配置信息', $system);
	}
}