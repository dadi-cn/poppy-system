<?php namespace Poppy\System\Classes\Sms;

use Exception;
use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Extension\Aliyun\Core\Profile\DefaultProfile;
use Poppy\Extension\Aliyun\Dysms\Sms\Request\V20170525\SendSmsRequest;
use SimpleXMLElement;
use Poppy\System\Classes\Contracts\Sms;

class AliyunSms extends BaseSms implements Sms
{
	/**
	 * @var DefaultAcsClient Acs 客户端
	 */
	public static $acsClient;

	public function __construct()
	{
		Config::load();
	}

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
		$sign    = sys_setting('system::sms.sign');
		$request = new SendSmsRequest();
		$request->setPhoneNumbers($mobiles);
		$request->setSignName($sign);
		$request->setTemplateCode($this->sms['code']);
		if ($params) {
			$request->setTemplateParam(json_encode($params, JSON_UNESCAPED_UNICODE));
		}

		try {
			$acsResponse = static::getAcsClient()->getAcsResponse($request);
			if ($acsResponse->Code && $acsResponse->Code == 'OK') {
				return true;
			}

			return $this->setError($acsResponse->Message);
		} catch (Exception $e) {
			return $this->setError(substr($e->getMessage(), 0, 255));
		}
	}

	/**
	 * 取得AcsClient
	 * @return DefaultAcsClient
	 */
	private static function getAcsClient(): DefaultAcsClient
	{
		$accessKeyId     = sys_setting('system::sms_aliyun.access_key');
		$accessKeySecret = sys_setting('system::sms_aliyun.access_secret');

		// 暂时不支持多Region
		// cn-hangzhou
		$region = 'cn-hangzhou';

		// 服务结点
		// cn-hangzhou
		$endPointName = 'cn-hangzhou';

		if (static::$acsClient === null) {
			//初始化acsClient,暂不支持region化
			$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

			// 增加服务结点
			DefaultProfile::addEndpoint($endPointName, $region, 'Dysmsapi', 'dysmsapi.aliyuncs.com');

			// 初始化AcsClient用于发起请求
			static::$acsClient = new DefaultAcsClient($profile);
		}

		return static::$acsClient;
	}
}
