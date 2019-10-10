<?php namespace Poppy\System\Classes\Sms;

use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\System\Models\SysConfig;

class BaseSms
{
	use AppTrait;

	/**
	 * @var DefaultAcsClient Acs 客户端
	 */
	public static $acsClient;

	/**
	 * @var array 短信
	 */
	protected $sms;

	/**
	 * 检查短信是否为空
	 * @param string $mobile 手机号
	 * @param string $type   类型
	 * @return bool
	 */
	public function checkSms($mobile, $type): bool
	{
		if (!$mobile) {
			return $this->setError('手机号缺失, 不进行发送!');
		}

		if (!$type) {
			return $this->setError($type . '短信模板不存在, 不进行发送');
		}
		$this->sms = SysConfig::smsTpl($type);

		if (!$this->sms) {
			return $this->setError('请设置短信模板');
		}

		return true;
	}
}
