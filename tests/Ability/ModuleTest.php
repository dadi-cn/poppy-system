<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Artisan;
use Poppy\System\Tests\Base\SystemTestCase;

class ModuleTest extends SystemTestCase
{


	public function testPages()
	{
		Artisan::call('cache:clear');
		$pages = app('module')->pages();
		dd($pages);
	}

	public function testRepos()
	{
		Artisan::call('cache:clear');
		$repo = app('module')->repository();
		dd($repo);
	}

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
