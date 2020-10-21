<?php namespace Poppy\System\Tests\Ability\Core;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\System\Tests\Ability\Jobs\StaticVarJob;

class ScalarTest extends TestCase
{

	public function testStaticVars(): void
	{
		dispatch(new StaticVarJob(1));
	}

}
