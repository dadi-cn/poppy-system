<?php namespace Poppy\System\Tests\Addon;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;

class AddonTest extends TestCase
{
	public function testPushAli()
	{
		dump(sys_addon('push/ali'));
	}
}
