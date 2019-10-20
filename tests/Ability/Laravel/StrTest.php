<?php namespace Poppy\System\Tests\Ability\Laravel;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Support\Str;
use Poppy\System\Tests\Base\SystemTestCase;

class StrTest extends SystemTestCase
{
	/**
	 * Diff 测试
	 */
	public function testCaseConvert(): void
	{
		$normal = 'api_v1';
		$studly = Str::studly($normal);
		$this->assertEquals('ApiV1', $studly);
	}

	/**
	 * 截取字符
	 */
	public function testCut(): void
	{
		$code = 'voice:' . md5('8') . '.mp3';
		$this->assertEquals('voice', Str::before($code, ':'));
	}
}