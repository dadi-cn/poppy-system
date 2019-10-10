<?php namespace Poppy\System\Services\Contracts;

/**
 * 数组
 */
interface ServiceArray
{
	/**
	 * key
	 * @return mixed
	 */
	public function key();

	/**
	 * 返回的数据
	 * @return mixed
	 */
	public function data();
}