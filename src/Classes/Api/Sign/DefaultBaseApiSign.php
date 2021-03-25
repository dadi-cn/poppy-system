<?php

namespace Poppy\System\Classes\Api\Sign;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\System\Classes\Contracts\ApiSignContract;

/**
 * 默认的 Timestamp 约定
 */
abstract class DefaultBaseApiSign implements ApiSignContract
{
    /**
     * 默认时间戳
     * @return int
     */
    public static function timestamp(): int
    {
        return (new DateTime())->getTimestamp();
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