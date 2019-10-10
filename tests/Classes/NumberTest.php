<?php namespace Poppy\System\Tests\Classes;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Classes\Number;
use Poppy\System\Tests\Base\SystemTestCase;

class NumberTest extends SystemTestCase
{
	public function testDivide(): void
	{
		$NumberB = new Number(5);
		$result  = (new Number(5))->divide($NumberB);
		$this->assertEquals('1.00', $result->getValue());
	}
}
