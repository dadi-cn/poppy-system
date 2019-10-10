<?php namespace Poppy\System\Tests\Configuration;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class PictureTest extends SystemTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->initPam();
	}

	/**
	 * 配置项检测
	 */
	public function testUpload()
	{
		$file   = base_path('modules/system/resources/images/system/spacer.gif');
		$result = $this->jsonPost('system/upload/image', 'v1', [
			'type'  => 'base64',
			'image' => base64_encode(file_get_contents($file)),
		]);
		$this->assertStatusSuccess($result);
	}
}
