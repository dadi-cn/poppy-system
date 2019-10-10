<?php namespace Poppy\System\Tests\Ability\Laravel;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class HelpersTest extends SystemTestCase
{
	public function testStr(): void
	{
		$code = 'voice:' . md5('8') . '.mp3';
		$this->assertEquals('voice', str_before($code, ':'));
	}
}