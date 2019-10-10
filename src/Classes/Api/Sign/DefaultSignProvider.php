<?php namespace Poppy\System\Classes\Api\Sign;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\System\Classes\Contracts\Api\Sign as SignContract;

/**
 * 后台用户认证
 */
class DefaultSignProvider implements SignContract
{
	use AppTrait;

	/**
	 * @var Request 请求内容
	 */
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 获取Sign
	 * @param array $params 请求参数
	 * @return string
	 */
	public function sign($params): string
	{
		$token = function () {
			$token = $this->request->header('Authorization');
			if ($token && starts_with($token, 'Bearer ')) {
				$token = substr($token, 7);
			}
			if (!$token) {
				$token = $this->request->input('token');
			}

			return $token;
		};
		ksort($params);
		$kvStr    = ArrayHelper::toKvStr($params);
		$signLong = md5(md5($kvStr) . $token());
		return $signLong[1] . $signLong[3] . $signLong[15] . $signLong[31];
	}


	public function check(): bool
	{
		$request = $this->request;
		/* 非正式环境并且未启用加密, 进行过滤
		 * ---------------------------------------- */
		if (!is_production() && !sys_setting('system::api.enable_sign')) {
			return true;
		}

		// check token
		$sign = $request->input('sign');
		if (!$sign) {
			return $this->setError(new Resp(Resp::PARAM_ERROR, '未进行签名'));
		}

		// check token
		$timestamp = $request->input('timestamp');
		if (!$timestamp) {
			return $this->setError(new Resp(Resp::PARAM_ERROR, '未传递时间戳'));
		}


		// check sign
		$sign   = $request->input('sign');
		$params = $request->except(['sign', 'image', 'token', '_token']);

		if ($sign !== $this->sign($params)) {
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
}