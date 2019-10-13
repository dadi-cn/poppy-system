<?php namespace Poppy\System\Tests\Ability\Core;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Support\Str;
use Poppy\Framework\Application\TestCase;
use ReflectionClass;
use Poppy\System\Setting\SettingServiceProvider;
use Throwable;

class ReflectionTest extends TestCase
{
	public function testMethodPath()
	{
		$className = SettingServiceProvider::class;
		// realpath : modules/system/src/setting/SettingServiceProvider.php
		try {
			$refection = new ReflectionClass($className);
		} catch (Throwable $e) {
			dump($e->getMessage());

			return;
		}

		$fileName = $refection->getFileName();
		// filename
		// output  : /data/workbench/www/play/modules/system/src/Setting/SettingServiceProvider.php

		$methods = $refection->getMethods();
		foreach ($methods as $method) {
			$methodName = $method->getName();

			// except extend
			if ($method->class !== $className) {
				continue;
			}

			// except magic
			if (Str::startsWith($methodName, '__')) {
				continue;
			}

			$methodFileName = $method->getFileName();
			// method file name
			// output : /data/workbench/www/play/modules/system/src/Setting/SettingServiceProvider.php
		}

		// the bug is
		// when use in mac os
		// the reflection filename is the wrong position
		// when it in method or file
	}
}
