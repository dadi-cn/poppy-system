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
class DefaultApiSignProvider implements ApiSignContract
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

    /**
     * js 计算
     * @return string
     */
    public static function js(): string
    {
        return <<<JS
        var params = [];
        var str = "";

        function _sign(tip) {
	        $("input[name=sign]").val(tip);
        }

        function _val(name) {
	        return $("input[name=" + name + "]").val();
        }

        $(".J_calc").each(function(i, ele) {
	        params.push($(ele).attr("name"));
        });

        params = _.without(params, "sign", "token", "image");
        params.sort();

        _.each(params, function(key) {
	        str += key + "=" + _val(key) + ","
        });
        str = str.slice(0, -1);
        
        var md5 = hex_md5(str);
        var token = _val("token");
        var step1 = str;
        var step2 = hex_md5(str)+_val("token");
        var md5Secret = hex_md5(hex_md5(str)+_val("token"));
        var md5Short = md5Secret.charAt(1) + md5Secret.charAt(3) + md5Secret.charAt(15) + md5Secret.charAt(31);
        console.warn("step 1(origin):"+step1+"\\n step2(md5 once):"+step2+"\\n step3(md5 twice):"+md5Secret+"\\n sign : "+ md5Short);
        _sign(md5Short);
JS;

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