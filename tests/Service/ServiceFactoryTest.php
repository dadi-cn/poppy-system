<?php namespace Poppy\System\Tests\Service;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\System\Services\Factory\ServiceFactory;

class ServiceFactoryTest extends TestCase
{

	public function testParse()
	{
		dump((new ServiceFactory())->parse('system.api_info'));
	}

	public function testParseForm()
	{
		dump((new ServiceFactory())->parse('ad.form_place_selection', [
			'name' => 'abc',
		]));
	}
}
