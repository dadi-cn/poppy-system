<?php namespace Poppy\System\Tests\Configuration;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\System\Tests\Base\SystemTestCase;

class SettingTest extends SystemTestCase
{
	use CoreTrait;
	/**
	 * 配置项检测
	 */
	public function testNullCheck()
	{
		$null_setting = [];
		$this->coreModule()->pages()->map(function ($module_setting) use (&$null_setting) {
			$tabs = $module_setting['tabs'] ?? collect();

			$module_title = $module_setting['title'] ?? '';
			$tabs->map(function ($group_setting) use (&$null_setting, $module_title) {
				$group_title = $group_setting['title'] ?? '';
				$fields      = $group_setting['fields'] ?? collect();
				$fields->map(function ($setting) use ($group_title, $module_title, &$null_setting) {
					$label = $setting['label'] ?? '';
					$key   = $setting['key'] ?? '';

					if ($setting['value'] !== '') {
						return;
					}
					$null_setting[] = [
						'title' => $module_title . ' >>> ' . $group_title . ' >>> ' . $label,
						'key'   => $key,
						'value' => $setting['value'],
					];
				});
			});
		});

		$json = json_encode($null_setting, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

		// 存在未设置的配置,停止
		$this->assertEmpty($null_setting, '有未设置的配置项: ' . $json);
	}
}
