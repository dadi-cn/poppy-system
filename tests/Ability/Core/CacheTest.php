<?php namespace Poppy\System\Tests\Ability\Core;

/**
 * Copyright (C) Update For IDE
 */

use Cache;
use Poppy\Framework\Application\TestCase;
use Poppy\System\Models\SysConfig;

class CacheTest extends TestCase
{

	public function testKeyMatch()
	{
		// 标签内部的数据外部是获取不到的
		sys_cache('test')->remember('abc', SysConfig::MIN_HALF_DAY, function () {
			return 5;
		});
		$a = Cache::get('abc');
		Cache::forget('abc');
		$b = Cache::get('abc');
		$this->assertEquals($a, $b);
	}

	/**
	 * 测试函数的保存和获取
	 */
	public function testFun()
	{
		sys_cache('order')->forever('test_fun', 5);
		$this->assertEquals(sys_cache('order')->get('test_fun'), 5);
		sys_cache('order')->forget('test_fun');
		$this->assertEquals(sys_cache('order')->get('test_fun'), null);
	}
}
