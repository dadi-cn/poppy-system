<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Artisan;
use Poppy\System\Tests\Base\SystemTestCase;

class ModuleTest extends SystemTestCase
{

	public function testMenus()
	{
		Artisan::call('cache:clear');
		$menus = app('module')->menus();
		dump($menus['system/backend']);
	}

	public function testUis()
	{
		sys_cache('system')->forget('system.modules.ui');
	}

}
