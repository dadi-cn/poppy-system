<?php namespace Poppy\System\Events;

use Poppy\System\Models\PamAccount;

/**
 * 用户禁用
 */
class PamDisableEvent
{
	/**
	 * @var PamAccount
	 */
	public $pam;

	/**
	 * @var object 附加的数据,便于以后追加数据
	 */
	public $append;


	/**
	 * PamDisableEvent constructor.
	 * @param PamAccount  $pam
	 * @param null|object $append
	 */
	public function __construct(PamAccount $pam, $append = null)
	{
		$this->pam    = $pam;
		$this->append = $append;
	}
}