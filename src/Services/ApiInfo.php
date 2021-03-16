<?php

namespace Poppy\System\Services;

use Poppy\Core\Services\Contracts\ServiceArray;

class ApiInfo implements ServiceArray
{

	/**
	 * @return mixed
	 */
	public function key()
	{
		return 'py-system';
	}

	/**
	 * @return mixed
	 */
	public function data()
	{
		return [
			'title' => '系统',
		];
	}
}