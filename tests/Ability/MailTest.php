<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Mail;
use Poppy\System\Mail\MaintainMail;
use Poppy\System\Mail\TestMail;
use Poppy\System\Tests\Base\SystemTestCase;
use Throwable;

class MailTest extends SystemTestCase
{
	/**
	 * 发送邮件
	 */
	public function testTest(): void
	{
		$email   = $this->env('mail');
		$content = '测试邮件发送';

		try {
			Mail::to($email)->send(new TestMail($content));
			$this->assertTrue(true);
		} catch (Throwable $e) {
			$this->assertFalse(false, $e->getMessage());
		}
	}

	/**
	 * 发送维护邮件
	 */
	public function testMaintain(): void
	{
		$email = $this->env('mail');

		try {
			Mail::to($email)->send(new MaintainMail('Mail Title', 'Mail Content'));
			$this->assertTrue(true);
		} catch (Throwable $e) {
			$this->assertFalse(false, $e->getMessage());
		}
	}
}