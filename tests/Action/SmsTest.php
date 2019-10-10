<?php namespace Poppy\System\Tests\Action;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Action\Sms;
use Poppy\System\Tests\Base\SystemTestCase;

/**
 * 发送短信
 */
class SmsTest extends SystemTestCase
{
	/**
	 * 测试短信发送
	 */
	public function testSendCaptcha(): void
	{
		$Sms = $this->action();
		if ($Sms->send('captcha', '15254109156', [
			'code' => 'Test_' . str_random(4),
		])) {
			$this->assertTrue(true);
		}
		else {
			$this->assertTrue(false, $Sms->getError());
		}
	}


	private function action(): Sms
	{
		return new Sms();
	}
}