<?php namespace Poppy\System\Http\Request\ApiV1\Web;

use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Captcha;
use Poppy\System\Action\Pam;
use Poppy\System\Action\Verification;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\SysCaptcha;
use Validator;

/**
 * 图像验证码控制器
 */
class CaptchaController extends WebApiController
{

	/**
	 * @api                 {get} api_v1/system/captcha/image 图像验证码
	 * @apiDescription      通过url地址过去验证图片, 用于发送验证码的流量限制
	 * @apiVersion          1.0.0
	 * @apiName             UtilCaptchaDisplay
	 * @apiGroup            System
	 * @apiParam   {string} mobile   手机号
	 */
	public function image()
	{
		$input     = [
			'mobile' => input('mobile'),
		];
		$validator = Validator::make($input, [
			'mobile' => 'required|mobile',
		], [], [
			'mobile' => '手机号',
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->messages(), 'json|1');
		}

		return (new Captcha())->generate($input['mobile']);
	}

	/**
	 * @api                 {post} api_v1/system/captcha/send 发送验证码
	 * @apiDescription      发送验证码(暂时支持持短信)
	 * @apiVersion          1.0.0
	 * @apiName             UtilSmsSend
	 * @apiGroup            System
	 * @apiParam   {string} [passport]    通行证 [给自己发验证码不需要填写]
	 * @apiParam   {string} type          操作类型
	 *             [login|登录;password|找回密码;user|给自己;rebind|绑定新手机;register|注册;bind_notice|绑定通知]
	 */
	public function send()
	{
		$passport     = input('passport', '');
		$type         = input('type', '');
		$passportType = (new Pam())->passportType($passport);

		switch ($type) {
			case SysCaptcha::CON_LOGIN:
			case SysCaptcha::CON_BIND_NOTICE:
				// 登录, 需要手机号, 需要图形验证码
				if (!$passport) {
					return Resp::web(Resp::ERROR, '请填入需要发送的通行证手机号');
				}
				break;
			case SysCaptcha::CON_REGISTER:
				if (PamAccount::passport($passport)) {
					return Resp::web(Resp::ERROR, '用户已经存在');
				}
				break;
			case SysCaptcha::CON_PASSWORD:

				// 需要验证手机号不为空
				if (!$passport) {
					return Resp::web(Resp::ERROR, '请填入需要发送的通行证手机号');
				}

				// 系统中需要存在这个账号
				if (!$account = PamAccount::where($passportType, $passport)->first()) {
					return Resp::web(Resp::ERROR, trans('system::action.captcha.account_miss'));
				}
				//设置过密码
				if (!$account->password) {
					return Resp::web(Resp::ERROR, trans('system::action.captcha.account_no_password'));
				}
				break;
			case SysCaptcha::CON_USER:
				// 需要授权
				$jwtUser = $this->jwtPam();
				if (!$jwtUser) {
					return Resp::web(Resp::ERROR, '你需要登录之后才能发送');
				}
				$passport = $jwtUser->mobile;
				break;
			case SysCaptcha::CON_REBIND:
				// 需要授权
				$jwtUser = $this->jwtPam();
				if (!$jwtUser) {
					return Resp::web(Resp::ERROR, '你需要登录之后才能发送');
				}

				// 需要验证手机号不为空
				if (!$passport) {
					return Resp::web(Resp::ERROR, '请填入需要发送的通行证手机号');
				}

				// 新绑定手机号不能存在
				if (PamAccount::where($passportType, $passport)->exists()) {
					return Resp::web(Resp::ERROR, trans('system::action.captcha.account_exists'));
				}
				break;
		}

		/** @var Verification $Verification */
		$Verification = new Verification();
		if (!$Verification->send($passport, $type)) {
			return Resp::web(Resp::ERROR, $Verification->getError());
		}

		$captcha = $Verification->getCaptcha();
		$tip     = trans('system::util.captcha.send_success');
		$tip     .= !is_production() ? ',' . $captcha->captcha : '';

		return Resp::web(Resp::SUCCESS, $tip);
	}

	/**
	 * @api                 {post} api_v1/system/captcha/verify_code 操作校验串
	 * @apiDescription      获取验证串, 以方便下一步操作
	 * @apiVersion          1.0.0
	 * @apiName             UtilCaptchaVerifyCode
	 * @apiGroup            System
	 * @apiParam   {string} passport      通行证
	 * @apiParam   {string} captcha       验证码
	 * @apiSuccessExample   data
	 * {
	 *     "verify_code": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*******"
	 * }
	 */
	public function verifyCode()
	{
		$passport = input('passport', '');
		$captcha  = input('captcha', '');

		$verify = new Verification();
		if (!$verify->check($passport, $captcha)) {
			return Resp::web(Resp::ERROR, $verify->getError());
		}

		$verify->delete($passport);

		return Resp::web(Resp::SUCCESS, '操作成功', [
			'verify_code' => $verify->genOnceVerifyCode(10, $passport),
		]);
	}
}