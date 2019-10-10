<?php namespace Poppy\System\Tests\Configuration;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class PoppyTest extends SystemTestCase
{
	/**
	 * 测试模块加载
	 */
	public function testLoaded()
	{
		$folders = glob(base_path('modules/*/src'), GLOB_BRACE);
		collect($folders)->each(function ($folder) {
			$matched = preg_match('/modules\/(?<module>[a-z]*)\/src/', $folder, $matches);
			if ($matched && !app('poppy')->exists($matches['module'])) {
				$this->assertTrue(false, "Module `{$matches['module']}` Not Exist , Please run `php artisan poppy:optimize` to fix.");
			}
			else {
				$this->assertTrue(true, "Module `{$matches['module']}` loaded.");
			}
		});
	}
}
