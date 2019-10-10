<?php namespace Poppy\System\Http\Request\Web;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;
use Poppy\System\Action\Captcha;

/**
 * 图像验证码控制器
 */
class CaptchaController extends WebController
{
	/**
	 * @param string $type 类型
	 * @param int    $width  宽度
	 * @param int    $height 高度
	 * @param int    $length 验证码长度
	 * @return ResponseFactory|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|Response
	 */
	public function image($type, $width = 180, $height = 50, $length = 4)
	{

		return (new Captcha())->session($type, $width, $height, $length);
	}
}