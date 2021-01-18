<?php namespace Poppy\System\Classes\Contracts;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;

interface ApiSignContract
{
	/**
	 * 获取Sign
	 * @param array $params 参数
	 * @return string
	 */
	public function sign(array $params): string;


	/**
	 * 检测签名
	 * @param Request $request
	 * @return bool
	 */
	public function check(Request $request): bool;


	/**
	 * 获取错误信息
	 * @return Resp
	 */
	public function getError();
}