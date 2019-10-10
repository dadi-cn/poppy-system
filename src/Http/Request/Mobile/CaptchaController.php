<?php namespace Poppy\System\Http\Request\Mobile;

use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Verification;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\SysCaptcha;

/**
 * 验证码发送
 */
class CaptchaController extends MobileController
{

	/**
	 * @api                     {post} mobile/system/captcha/send 发送验证码
	 * @apiDescription          发送短信验证码
	 * @apiVersion              1.0.0
	 * @apiName                 SystemCaptchaSend
	 * @apiGroup                System
	 * @apiParam   {string}     mobile    通行证
	 * @apiParam   {string}     [type]    类型, 默认为注册 [register:注册;login:登录]
	 */
	public function send()
	{
		sys_container()->setExecutionContext('api');
		$mobile = input('mobile', '');
		$type   = input('type', 'register');
		if (!$mobile) {
			return Resp::error('请填入需要发送的通行证手机号');
		}

		if ($type === SysCaptcha::CON_REGISTER && PamAccount::passport($mobile)) {
			return Resp::error('用户已经存在');
		}

		/** @var Verification $Verification */
		$Verification = new Verification();
		if (!$Verification->send($mobile, '')) {
			return Resp::error($Verification->getError());
		}

		$captcha = $Verification->getCaptcha();
		$tip     = trans('system::util.captcha.send_success');
		$tip     .= !is_production() ? ',' . $captcha->captcha : '';

		return Resp::success($tip);
	}
}
