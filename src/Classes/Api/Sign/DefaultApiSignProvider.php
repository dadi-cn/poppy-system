<?php

namespace Poppy\System\Classes\Api\Sign;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\System\Classes\Contracts\ApiSignContract;

/**
 * 后台用户认证
 */
class DefaultApiSignProvider extends DefaultBaseApiSign implements ApiSignContract
{
    use AppTrait;

    /**
     * @var Request 请求内容
     */
    private $request;

    public function __construct()
    {
        $this->request = app('request');
    }

    /**
     * 获取Sign
     * @param array $params 请求参数
     * @return string
     */
    public function sign(array $params): string
    {
        $dirtyParams = $params;
        $params      = $this->except($params);
        $token       = function ($params) {
            $token = $this->request->header('Authorization');
            if ($token && Str::startsWith($token, 'Bearer ')) {
                $token = substr($token, 7);
            }
            if (!$token) {
                $token = $this->request->input('token');
            }

            if (!$token) {
                $token = $params['token'] ?? '';
            }
            return $token;
        };
        ksort($params);
        $kvStr    = ArrayHelper::toKvStr($params);
        $signLong = md5(md5($kvStr) . $token($dirtyParams));
        return $signLong[1] . $signLong[3] . $signLong[15] . $signLong[31];
    }


    public function check(Request $request): bool
    {
        // check token
        $timestamp = $request->input('timestamp');
        if (!$timestamp) {
            return $this->setError(new Resp(Resp::PARAM_ERROR, '未传递时间戳'));
        }

        // 加密 debug, 不验证签名
        if (config('poppy.system.secret') && $request->input('_py_sys_secret') === config('poppy.system.secret')) {
            return true;
        }

        // check token
        $sign = $request->input('sign');
        if (!$sign) {
            return $this->setError(new Resp(Resp::PARAM_ERROR, '未进行签名'));
        }

        // check sign
        if ($sign !== $this->sign($request->all())) {
            return $this->setError(new Resp(Resp::SIGN_ERROR, '签名错误'));
        }
        return true;
    }

    private function except($params): array
    {
        $excepts = [];
        foreach ($params as $key => $param) {
            if (!Str::startsWith($key, '_')) {
                $excepts[$key] = $param;
            }
        }
        return Arr::except($excepts, [
            'sign', 'image', 'file', 'token',
        ]);
    }
}