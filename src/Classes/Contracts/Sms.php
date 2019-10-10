<?php namespace Poppy\System\Classes\Contracts;

/**
 * 短信实现
 */
interface Sms
{
	/**
	 * 发送短信
	 * @param string       $type    发送类型
	 * @param array|string $mobiles 接收手机号
	 * @param array        $params  参数
	 * @return mixed
	 */
	public function send($type, $mobiles, array $params = []): bool;

	/**
	 * @return mixed 错误信息
	 */
	public function getError();
}