<?php namespace Poppy\System\Events;

use Poppy\System\Models\PamAccount;

/**
 * 登录成功事件
 */
class LoginSuccessEvent
{
	/**
	 * @var PamAccount 用户账户
	 */
	public $pam;

	/**
	 * @var string 平台
	 */
	public $platform;

	public function __construct(PamAccount $pam, $platform)
	{
		$this->pam      = $pam;
		$this->platform = $platform;
	}
}