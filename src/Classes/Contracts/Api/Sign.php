<?php namespace Poppy\System\Classes\Contracts\Api;

use Poppy\Framework\Classes\Resp;

interface Sign
{
	/**
	 * 获取Sign
	 * @param array $params 参数
	 * @return string
	 */
	public function sign($params): string;


	/**
	 * 检测签名
	 * @return bool
	 */
	public function check(): bool;


	/**
	 * 获取错误信息
	 * @return Resp
	 */
	public function getError();
}