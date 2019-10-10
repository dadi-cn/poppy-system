<?php namespace Poppy\System\Action;

use Carbon\Carbon;
use Exception;
use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Models\SysCaptcha;

/**
 * 系统校验
 */
class Verification
{
	use AppTrait;

	const CRYPT_METHOD = 'AES-256-ECB';

	/**
	 * @var string 隐藏在加密中的字符串
	 */
	private $hiddenStr;

	/**
	 * @var SysCaptcha
	 */
	private $captcha;

	/**
	 * @var DefaultAcsClient Acs 客户端
	 */
	private static $acsClient;

	public function __construct()
	{
		Config::load();
	}

	/**
	 * @param string $passport 需要发送的通行证
	 * @param string $type     发送验证码的类型
	 * @return bool
	 */
	public function send($passport, $type): bool
	{
		// 验证数据格式
		if (UtilHelper::isEmail($passport)) {
			$passportType = SysCaptcha::TYPE_MAIL;
			$expiredMin   = sys_setting('system::captcha.mail_expired_minute');
		}
		elseif (UtilHelper::isMobile($passport)) {
			$passportType = SysCaptcha::TYPE_MOBILE;
			$expiredMin   = sys_setting('system::sms.expired_minute');
		}
		else {
			if (is_numeric($passport)) {
				return $this->setError('请输入正确的手机号');
			}
			return $this->setError(trans('system::action.verification.send_passport_format_error'));
		}

		/* 验证码默认时长 5 分钟
		 * ---------------------------------------- */
		if (!$expiredMin) {
			$expiredMin = 5;
		}

		// 发送验证码数据库操作
		$Captcha = SysCaptcha::where('passport', $passport)->first();
		if ($Captcha && $Captcha->updated_at > Carbon::now()->subMinute(1)) {
			return $this->setError('发送验证码操作过于频繁，请稍后尝试');
		}
		$expired = Carbon::now()->addMinute($expiredMin);
		/** @var SysCaptcha $captcha */
		$captcha = SysCaptcha::updateOrCreate([
			'passport' => $passport,
		]);
		if ($captcha->num === 3) {
			$captcha->num = 0;
		}
		// Not Send
		if ((int) $captcha->num === 0) {
			$captcha->num     = 1;
			$captcha->type    = $passportType;
			$captcha->captcha = StrHelper::randomCustom(6, '0123456789');
		}
		else {
			$captcha->num  += 1;
			$captcha->type = $passportType;
		}
		$captcha->disabled_at = $expired;
		$captcha->save();

		$this->captcha = $captcha;

		// 清除失效的验证码
		sys_cacher('system.action.verification-clear', function () {
			SysCaptcha::where('disabled_at', '<', Carbon::now())->delete();
		}, 1000);

		if ($passportType === SysCaptcha::TYPE_MOBILE) {
			if (!$this->sendSms($passport, $captcha->captcha)) {
				return false;
			}
		}
		else {
			$this->sendMail($passport, $type);
		}

		return true;
	}

	/**
	 * 验证验证码
	 * @param string $passport 通行证
	 * @param string $captcha  验证码
	 * @return bool
	 */
	public function check($passport, $captcha): bool
	{
		/* 测试账号验证码/正确的验证码即可登录
		 * ---------------------------------------- */
		$strAccount = trim(sys_setting('system::pam.test_account'));
		if ($strAccount) {
			$explode = EnvHelper::isWindows() ? "\n" :  PHP_EOL;
			$testAccount = explode($explode, sys_setting('system::pam.test_account'));
			if (count($testAccount)) {
				$testAccount = collect(array_map(function ($item) {
					$account = explode(':', $item);

					return [
						'passport' => trim($account[0] ?? ''),
						'captcha'  => trim($account[1] ?? ''),
					];
				}, $testAccount));
				$item        = $testAccount->where('passport', $passport)->first();
				if ($item) {
					$captcha      = (string) $captcha;
					$savedCaptcha = (string) ($item['captcha'] ?? '');
					if ($savedCaptcha && $captcha !== $savedCaptcha) {
						return $this->setError('验证码不正确!');
					}

					return true;
				}
			}
		}

		$type = SysCaptcha::TYPE_MOBILE;
		if (UtilHelper::isEmail($passport)) {
			$type = SysCaptcha::TYPE_MAIL;
		}

		$captcha = SysCaptcha::where([
			'captcha'  => $captcha,
			'passport' => $passport,
			'type'     => $type,
		])->first();
		if ($captcha) {
			if ($captcha->disabled_at < Carbon::now()) {
				return $this->setError('验证码已过期, 请重新发送');
			}

			return true;
		}

		return $this->setError(trans('system::action.verification.check_captcha_error'));
	}

	/**
	 * 删除验证码
	 * @param string $passport 通行证
	 * @return bool|null
	 */
	public function delete($passport): bool
	{
		try {
			return SysCaptcha::where('passport', $passport)->delete();
		} catch (Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 清除过期验证码
	 * @return bool|null
	 */
	public function clean()
	{
		try {
			return SysCaptcha::where('disabled_at', '<', Carbon::now())->delete();
		} catch (Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 生成一次验证码
	 * @param int    $expired_min 过期时间
	 * @param string $hidden_str  隐藏的验证字串
	 * @return string
	 */
	public function genOnceVerifyCode($expired_min = 10, $hidden_str = ''): string
	{
		// 生成 10 分钟的有效 code
		$now     = Carbon::now();
		$unix    = Carbon::now()->addMinutes($expired_min)->timestamp;
		$randStr = str_random(16);
		$key     = $now->timestamp . '_' . str_random(6);
		if (!$hidden_str) {
			$hidden_str = str_random(6);
		}
		$str      = $unix . '|' . $key . '|' . $hidden_str . '|' . $randStr;
		$cacheKey = 'system.action.verification.once_code';
		if (sys_cache('system')->has($cacheKey)) {
			$data = sys_cache('system')->get($cacheKey);
			if (is_array($data)) {
				foreach ($data as $item) {
					[$_unix, $_key] = explode('|', $item);
					if ($_unix < $now->timestamp) {
						// key 已经过期, 移除
						unset($data[$_key]);
					}
				}
			}
			$data[$key] = $str;
		}
		else {
			$data = [
				$key => $str,
			];
		}
		sys_cache('system')->forever($cacheKey, $data);

		return openssl_encrypt($str, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
	}

	/**
	 * 需要验证的验证码
	 * @param string $verify_code 一次验证码
	 * @return bool
	 */
	public function verifyOnceCode($verify_code)
	{
		$str = openssl_decrypt($verify_code, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
		if (strpos($str, '|') !== false) {
			$split    = explode('|', $str);
			$expire   = (int) $split[0];
			$key      = (string) $split[1];
			$cacheKey = 'system.action.verification.once_code';
			$data     = (array) sys_cache('system')->get($cacheKey);
			if ($expire < Carbon::now()->timestamp) {
				return $this->setError(trans('system::action.verification.verify_code_expired'));
			}

			if (!isset($data[$key])) {
				return $this->setError(trans('system::action.verification.verify_code_expired'));
			}
			unset($data[$key]);
			$this->hiddenStr = $split[2];
			sys_cache('system')->forever($cacheKey, $data);

			return true;
		}

		return $this->setError(trans('system::action.verification.verify_code_error'));
	}

	/**
	 * @return string
	 */
	public function getHiddenStr(): string
	{
		return $this->hiddenStr;
	}

	/**
	 * @return SysCaptcha
	 */
	public function getCaptcha(): SysCaptcha
	{
		return $this->captcha;
	}

	/**
	 * 发送短信
	 * @param string $phoneNumbers 电话号码
	 * @param string $captcha      验证码
	 * @return bool
	 */
	private function sendSms($phoneNumbers, $captcha): bool
	{
		$param = [
			'code' => $captcha,
		];
		$Sms   = new Sms();
		if ($Sms->send('captcha', $phoneNumbers, $param)) {
			return true;
		}

		return $this->setError($Sms->getError());
	}

	/**
	 * 发送邮件
	 * @param string $passport    通行证
	 * @param string $rand_number 验证码
	 */
	private function sendMail($passport, $rand_number)
	{
	}
}
