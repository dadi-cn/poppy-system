<?php namespace Poppy\System\Action;

use Cache;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Contracts\Routing\ResponseFactory;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\AppTrait;
use Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * 验证码
 */
class Captcha
{
	use AppTrait;

	/**
	 * 图像验证码验证
	 * @param string $mobile  手机号
	 * @param string $captcha 验证码
	 * @return bool
	 */
	public function check($mobile, $captcha)
	{
		if (!$captcha) {
			return $this->setError(trans('system::action.image_captcha.check_not_input'));
		}

		// 非开发环境下不检测验证码
		if (!is_production()) {
			return true;
		}
		$captchaCache = Cache::pull('captcha_' . $mobile);
		if (!$captchaCache) {
			return $this->setError(trans('system::action.image_captcha.check_not_exist'));
		}

		if (strtolower($captchaCache) !== strtolower($captcha)) {
			return $this->setError(trans('system::action.image_captcha.check_error'));
		}

		return true;
	}

	/**
	 * @param string $mobile 手机号
	 * @param int    $length 验证码长度
	 * @return ResponseFactory|Response
	 */
	public function generate($mobile, $length = 4)
	{
		$phraseBuilder = new PhraseBuilder($length);
		//生成验证码图片的Builder对象，配置相应属性
		$builder = new CaptchaBuilder(null, $phraseBuilder);
		//可以设置图片宽高及字体
		$builder->build($width = 180, $height = 50, $font = null);
		//获取验证码的内容
		$phrase = $builder->getPhrase();

		//把内容存入 缓存
		Cache::put('captcha_' . $mobile, $phrase, 5);
		ob_clean();
		ob_start();
		//生成图片
		$builder->output();
		$content = ob_get_clean();

		return response($content, 200, [
			'Content-Type'  => 'image/png',
			'Cache-Control' => 'no-cache, must-revalidate',
		]);
	}

	/**
	 * 图像验证码验证
	 * @param string $type    验证码类型
	 * @param string $captcha 验证码
	 * @return bool
	 */
	public function sessionCheck($type, $captcha): bool
	{
		if (!$captcha) {
			return $this->setError(trans('system::action.image_captcha.check_not_input'));
		}

		$phrase = Session::get('captcha--' . $type);
		if (!$phrase) {
			return $this->setError(trans('system::action.image_captcha.check_not_exist'));
		}

		if (strtolower($phrase) !== strtolower($captcha)) {
			return $this->setError(trans('system::action.image_captcha.check_error'));
		}

		Session::forget('captcha--' . $type);
		return true;
	}

	/**
	 * 生成网页验证码
	 * @param string $type   验证码类型
	 * @param int    $width  宽度
	 * @param int    $height 高度
	 * @param int    $length 验证码长度
	 * @return ResponseFactory|Response
	 */
	public function session($type, $width = 180, $height = 50, $length = 4)
	{
		$phraseBuilder = new PhraseBuilder($length);
		$builder       = new CaptchaBuilder(null, $phraseBuilder);
		$builder->build($width, $height, $font = null);
		$phrase = $builder->getPhrase();

		Session::put('captcha--' . $type, $phrase);
		ob_clean();
		ob_start();
		//生成图片
		$builder->output();
		$content = ob_get_clean();

		return response($content, 200, [
			'Content-Type'  => 'image/jpeg',
			'Cache-Control' => 'no-cache, must-revalidate',
		]);
	}
}