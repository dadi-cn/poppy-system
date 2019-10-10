<?php namespace Poppy\System\Http\Request\ApiV1\Web;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\System\Action\Verification;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\Resources\PamResource;
use Poppy\System\Models\SysConfig;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

/**
 * 认证控制器
 */
class AuthController extends WebApiController
{
	use PoppyTrait, ThrottlesLogins;

	/**
	 * @api                  {post} api_v1/system/auth/access 检测 Token 是否过期
	 * @apiVersion           1.0.0
	 * @apiName              PamAuthAccess
	 * @apiGroup             System
	 * @apiParam {string}    token            Token
	 * @apiSuccess {int}        id              ID
	 * @apiSuccess {string}     username        用户名
	 * @apiSuccess {string}     mobile          手机号
	 * @apiSuccess {string}     email           邮箱
	 * @apiSuccess {string}     type            用户类型
	 * @apiSuccess {int}            is_enable       启用/禁用
	 * @apiSuccessExample    data
	 * {
	 *     "id": 3,
	 *     "username": "fadan001",
	 *     "mobile": "18766482988",
	 *     "email": "",
	 *     "password": "34e6ffe64017f5ff509814f7106d3c0c",
	 *     "type": "user",
	 *     "is_enable": 1,
	 *     "disable_reason": "",
	 *     "created_at": "2018-01-02 16:08:01",
	 *     "updated_at": "2018-01-31 10:28:13"
	 * }
	 */
	public function access(): JsonResponse
	{
		try {
			if (!$user = app('tymon.jwt.auth')->parseToken()->authenticate()) {
				return $this->getResponse()->json([
					'message' => '登录失效，请重新登录！',
				], 401, [], JSON_UNESCAPED_UNICODE);
			}
		} catch (JWTException $e) {
			return $this->getResponse()->json([
				'message' => 'Token 错误',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		return Resp::success(
			'有效登录',
			(new PamResource($user))->toArray($this->getRequest())
		);
	}

	/**
	 * @api                    {post} api_v1/system/auth/token/:guard 获取 Jwt Token
	 * @apiVersion             1.0.0
	 * @apiName                PamAuthToken
	 * @apiGroup               System
	 * @apiParam {string}      :guard          web|Web;backend|后台;
	 * @apiParam {string}      passport        通行证
	 * @apiParam {string}      password        密码
	 * @apiSuccess {string}    token           认证成功的Token
	 * @apiSuccessExample      data
	 * {
	 *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*******"
	 * }
	 */
	public function token($guard = 'web'): JsonResponse
	{

		$validator = Validator::make($this->getRequest()->all(), [
			'passport' => Rule::required(),
			'password' => Rule::required(),
		], [
			'passport.required' => '用户名必须填写',
			'password.required' => '用户密码必须填写',
		]);
		if ($validator->fails()) {
			return Resp::error($validator->messages());
		}

		if ($this->hasTooManyLoginAttempts($this->getRequest())) {
			$seconds = $this->limiter()->availableIn($this->throttleKey($this->getRequest()));
			$message = $this->getTranslator()->get('auth.throttle', ['seconds' => $seconds]);

			return $this->getResponse()->json([
				'message' => $message,
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		$credentials = (new Pam())->passportData($this->getRequest());

		if (!$this->guard($guard)->attempt($credentials)) {
			return $this->getResponse()->json([
				'message' => '认证失败',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		$this->clearLoginAttempts($this->getRequest());
		/** @var PamAccount $user */
		$user = $this->guard($guard)->user();

		if ($user->is_enable === SysConfig::NO) {
			return $this->getResponse()->json([
				'message' => '用户被禁用, 原因 : ' . $user->disable_reason . ', 解禁时间 : ' . $user->disable_end_at,
				'status'  => Resp::ERROR,
			], 200, [], JSON_UNESCAPED_UNICODE);
		}

		if (!$token = app('tymon.jwt.auth')->fromUser($user)) {
			return $this->getResponse()->json([
				'message' => '凭证有误',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		return $this->getResponse()->json([
			'token'   => $token,
			'message' => '认证通过',
			'status'  => Resp::SUCCESS,
		], 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @api                    {post} api_v1/system/auth/reset_password 重设密码
	 * @apiVersion             1.0.0
	 * @apiName                PamResetPassword
	 * @apiGroup               System
	 * @apiParam {string}      verify_code     验证串
	 * @apiParam {string}      password        密码
	 */
	public function resetPassword()
	{
		$verify_code = input('verify_code', '');
		$password    = input('password', '');

		$Verification = new Verification();
		if ($Verification->verifyOnceCode($verify_code)) {
			$passport = $Verification->getHiddenStr();
			$Pam      = new Pam();
			$pam      = PamAccount::passport($passport);
			if ($Pam->setPassword($pam, $password)) {
				return Resp::success('密码已经重新设置');
			}

			return Resp::error($Pam->getError());
		}

		return Resp::error($Verification->getError());
	}

	/**
	 * @api                    {post} api_v1/system/auth/login 登录
	 * @apiVersion             1.0.0
	 * @apiName                PamAuthLogin
	 * @apiGroup               System
	 * @apiParam {string}      passport      通行证
	 * @apiParam {string}      [captcha]     验证码
	 * @apiParam {string}      [password]    密码
	 * @apiParam {string}      [platform]    登录平台
	 * @apiSuccess {string}    token         Token
	 * @apiSuccessExample      data
	 * {
	 *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*****.****-****",
	 * }
	 */
	public function login()
	{
		$passport = input('passport', '');
		$captcha  = input('captcha', '');
		$password = input('password', '');
		$platform = input('platform', '');

		if (!$captcha && !$password) {
			return Resp::error(trans('user::action.profile.login_key_need'));
		}

		/** @var Pam $Pam */
		$Pam = new Pam();
		if ($captcha) {
			if (!$Pam->captchaLogin($passport, $captcha, $platform)) {
				return Resp::error($Pam->getError());
			}
		}
		elseif (!$Pam->loginCheck($passport, $password, PamAccount::GUARD_JWT_WEB)) {
			return Resp::error($Pam->getError());
		}
		$pam = $Pam->getPam();

		if (!$token = app('tymon.jwt.auth')->fromUser($pam)) {
			return Resp::error(trans('user::login.graphql.get_token_fail'));
		}

		return Resp::success('登录成功', [
			'token' => $token,
		]);
	}

	/**
	 * @param string $guard 支持的guard 类型
	 * @return Guard|StatefulGuard
	 */
	protected function guard($guard)
	{
		if ($guard === 'web') {
			$guard = PamAccount::GUARD_JWT_WEB;
		}
		else {
			$guard = PamAccount::GUARD_JWT_BACKEND;
		}

		return $this->getAuth()->guard($guard);
	}

	/**
	 * @return string
	 */
	protected function username(): string
	{
		return 'passport';
	}
}