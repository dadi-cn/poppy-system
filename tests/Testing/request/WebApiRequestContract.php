<?php

namespace Poppy\System\Tests\Testing\Request;

use Carbon\Carbon;
use Curl\Curl;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\System\Classes\Contracts\ApiFactoryContract;
use Throwable;

/**
 * 生成Token
 */
class WebApiRequestContract extends BaseApiRequest implements ApiFactoryContract
{
    /**
     * @var array 跳过检测的URL地址
     */
    protected $jumpUrl = [
        'api_v1/system/auth/logout',
    ];

    /**
     * 生成参数
     * @param array $params 生成参数
     * @return array
     */
    public function genParams($params = []): array
    {
        if (!$params) {
            $params = $this->genParamsFromComment();
        }
        $params['timestamp'] = Carbon::now()->timestamp;
        ksort($params);
        $kvStr           = ArrayHelper::toKvStr($params);
        $signLong        = md5(md5($kvStr) . self::$token);
        $params['token'] = self::$token;
        $params['sign']  = $signLong[1] . $signLong[3] . $signLong[15] . $signLong[31];

        return $params;
    }

    /**
     * @param $url
     * @throws Throwable
     */
    protected function login($url = ''): void
    {
        $Curl     = new Curl();
        $passport = env('TESTING_WEB_API_PASSPORT');
        $captcha  = env('TESTING_WEB_API_CAPTCHA');
        if (!$passport) {
            throw new ApplicationException('测试账号必须填写');
        }
        if (!$captcha) {
            throw new ApplicationException('测试验证码必须填写');
        }

        if ($resp = $Curl->post($url . '/api_v1/system/auth/login', $this->genParams([
            'passport' => $passport,
            'captcha'  => $captcha,
        ]))) {
            $status = data_get($resp, 'status');
            if ($status) {
                throw new ApplicationException(data_get($resp, 'message'));
            }

            self::$token = data_get($resp, 'data.token');
        }
    }

    /**
     * 从注释中生成请求参数
     * @return array
     */
    private function genParamsFromComment(): array
    {
        $params = data_get($this->definition, 'parameter.fields.Parameter');
        if (!$params) {
            return [];
        }
        $calcParams = [];
        foreach ($params as $param) {
            if (in_array($param->field, ['token', ''], true)) {
                continue;
            }
            $calcParams[$param->field] = '';
        }

        return $calcParams;
    }
}