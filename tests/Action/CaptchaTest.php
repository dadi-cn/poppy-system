<?php namespace Poppy\System\Tests\Action;

use Poppy\System\Action\Verification;
use Poppy\System\Models\SysCaptcha;
use Poppy\System\Tests\Base\SystemTestCase;

class CaptchaTest extends SystemTestCase
{
	public function testSend(): bool
	{
		$Verification = new Verification();
		$mobile       = $this->faker()->phoneNumber;
		if (!$Verification->send($mobile, SysCaptcha::CON_LOGIN)) {
			$this->assertTrue(false, $Verification->getError());
		}
		else {
			$item = SysCaptcha::where('passport', $mobile)->first();
			$this->assertNotNull($item);
		}
	}
}
