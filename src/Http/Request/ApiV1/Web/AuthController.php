<?php

namespace Poppy\System\Http\Request\ApiV1\Web;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\System\Action\Verification;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\Resources\PamResource;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

/**
 * 认证控制器
 */
class AuthController extends WebApiController
{
    use PoppyTrait, ThrottlesLogins;

    /**
     * @api              {post} api_v1/system/auth/access 检测 Token
     * @apiVersion       1.0.0
     * @apiName          PamAuthAccess
     * @apiGroup         System
     *
     * @apiParam {int}   token            Token
     *
     * @apiSuccess {int}      id              ID
     * @apiSuccess {string}   username        用户名
     * @apiSuccess {string}   mobile          手机号
     * @apiSuccess {string}   email           邮箱
     * @apiSuccess {string}   type            类型
     * @apiSuccess {string}   is_enable       是否启用[Y|N]
     * @apiSuccess {string}   disable_reason  禁用原因
     * @apiSuccess {string}   created_at      创建时间
     * @apiSuccessExample {json} data:
     * {
     *     "id": 9,
     *     "username": "user001",
     *     "mobile": "",
     *     "email": "",
     *     "type": "user",
     *     "is_enable": "Y",
     *     "disable_reason": "",
     *     "created_at": "2021-03-18 15:30:15",
     *     "updated_at": "2021-03-18 16:38:06"
     * }
     */
    public function access(): JsonResponse
    {
        /** @var ResponseFactory $response */
        $response = app(ResponseFactory::class);
        try {
            if (!$user = app('tymon.jwt.auth')->parseToken()->authenticate()) {
                return $response->json([
                    'message' => '登录失效，请重新登录！',
                    'status'  => 401,
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (JWTException $e) {
            return $response->json([
                'message' => 'Token 错误',
                'status'  => 401,
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        return Resp::success(
            '有效登录',
            (new PamResource($user))->toArray(app('request'))
        );
    }

    /**
     * @api                    {post} api_v1/system/auth/login 登录/注册
     * @apiVersion             1.0.0
     * @apiName                PamAuthToken
     * @apiGroup               System
     * @apiParam {string}      guard           登录类型;web|Web;backend|后台;
     * @apiParam {string}      passport        通行证
     * @apiParam {string}      [password]      密码
     * @apiParam {string}      [captcha]       验证码
     * @apiSuccess {string}    token           认证成功的Token
     * @apiSuccess {string}    type            账号类型
     * @apiSuccessExample      data
     * {
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*******",
     *     "type": "backend"
     * }
     */
    public function login(): JsonResponse
    {
        $validator = Validator::make($this->pyRequest()->all(), [
            'passport' => Rule::required(),
        ], [
            'passport.required' => '通行证必须填写',
        ]);
        if ($validator->fails()) {
            return Resp::error($validator->messages());
        }

        $passport = input('passport', '');
        $captcha  = input('captcha', '');
        $password = input('password', '');
        $platform = input('platform', '');
        $guard    = input('guard', '');

        if ($guard === 'web') {
            $guard = PamAccount::GUARD_JWT_WEB;
        }
        else {
            $guard = PamAccount::GUARD_JWT_BACKEND;
        }

        if (!$captcha && !$password) {
            return Resp::error('登录密码或者验证码必须填写');
        }

        /** @var ResponseFactory $response */
        $response = app(ResponseFactory::class);
        if ($this->hasTooManyLoginAttempts($this->pyRequest())) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($this->pyRequest()));
            $message = $this->pyTranslator()->get('auth.throttle', ['seconds' => $seconds]);

            return $response->json([
                'message' => $message,
                'status'  => 401,
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $Pam = new Pam();
        if ($captcha) {
            if (!$Pam->captchaLogin($passport, $captcha, $platform)) {
                return Resp::error($Pam->getError());
            }
        }
        elseif (!$Pam->loginCheck($passport, $password, $guard)) {
            return Resp::error($Pam->getError());
        }

        $this->clearLoginAttempts($this->pyRequest());
        $pam = $Pam->getPam();

        if (!$token = app('tymon.jwt.auth')->fromUser($pam)) {
            return Resp::error('获取 Token 失败, 请联系管理员');
        }

        return Resp::success('认证通过', [
            'token' => $token,
            'type'  => $pam->type,
        ]);
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


    protected function username(): string
    {
        return 'passport';
    }
}