<?php namespace Poppy\System\Tests\Ability\Laravel;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class HelpersTest extends SystemTestCase
{
	/**
	 * 你需要传一个值和一个回调到方法中，值作为回调的参数，回调将执行，最后值被返回。
	 * @url https://learnku.com/articles/3893/laravel-tap-usage
	 */
	public function testTap(): void
	{
		$newValue = '';
		$tapValue = tap('abc', function ($value) use (&$newValue) {
			$newValue = $value . '-1';
		});
		$this->assertEquals('abc-1', $newValue);
		$this->assertEquals('abc', $tapValue);
	}
}