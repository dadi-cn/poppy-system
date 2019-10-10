<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class FunctionTest extends SystemTestCase
{
	/**
	 * 测试 oss 上传
	 */
	public function testDbComment(): void
	{
		$comment = sys_db('area_content.code');
		$this->assertEquals('编码', $comment, 'Db Comment Fetch failed.');
	}

	public function testCacher(): void
	{
		sys_cacher('system.action.verification-clear', function () {
			echo '3';
		}, 5);
	}

	/**
	 * 缓存测试, 带标签的使用 Flush 来清除标签缓存
	 */
	public function testCache(): void
	{
		sys_cache('system')->forever('system.test.sys_cache', 5);
		sys_cache()->forever('test.sys_cache', 8);
		$this->assertEquals(sys_cache('system')->get('system.test.sys_cache'), 5);
		sys_cache('system')->flush();
		$this->assertEquals(sys_cache()->get('test.sys_cache'), 8);
		$this->assertEquals(sys_cache('system')->get('system.test.sys_cache'), null);
	}
}