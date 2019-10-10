<?php namespace Poppy\System\Classes\Sms;

use Log;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use SimpleXMLElement;
use Poppy\System\Classes\Contracts\Sms;

/**
 * Class LocalSms
 */
class LocalSms extends BaseSms implements Sms
{
	/**
	 * @var DefaultAcsClient Acs 客户端
	 */
	public static $acsClient;

	/**
	 * @param array|string $mobiles 手机号码
	 * @param string       $type    模版代码
	 * @param array        $params  额外参数
	 * @return mixed|SimpleXMLElement
	 */
	public function send($type, $mobiles, array $params = []): bool
	{
		if (!$this->checkSms($mobiles, $type)) {
			return false;
		}
		// 未选择则使用日志, 线上不记录日志
		$sign    = sys_setting('system::sms.sign');
		$trans   = sys_trans($this->sms['content'], $params);
		$content = ($sign ? "[{$sign}]" : '') . $trans;
		Log::info(sys_mark('system', self::class, $content, true));

		return true;
	}
}
