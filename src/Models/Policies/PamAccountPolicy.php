<?php namespace Poppy\System\Models\Policies;

use Poppy\System\Models\PamAccount;
use Poppy\System\Models\SysConfig;

/**
 * PamAccount 策略
 */
class PamAccountPolicy
{
	/**
	 * @var string 管理权限
	 */
	private $manage = 'backend:global.pam.manage';

	/**
	 * @param PamAccount $pam     账户
	 * @param string     $ability 能力
	 * @return bool|null
	 */
	public function before(PamAccount $pam, $ability)
	{
		if (!$pam->hasRole('root')) {
			return $pam->capable($this->manage);
		}

		return null;
	}

	/**
	 * 编辑
	 * @param PamAccount $pam 账号
	 * @return bool
	 */
	public function create(PamAccount $pam)
	{
		return true;
	}

	/**
	 * 编辑
	 * @param PamAccount $pam  账号
	 * @param PamAccount $item 账号
	 * @return bool
	 */
	public function edit(PamAccount $pam, PamAccount $item)
	{
		return true;
	}

	/**
	 * 保存权限
	 * @param PamAccount $pam  账号
	 * @param PamAccount $item 账号
	 * @return bool
	 */
	public function enable(PamAccount $pam, PamAccount $item)
	{
		return (int) $item->is_enable === SysConfig::NO;
	}

	/**
	 * 删除
	 * @param PamAccount $pam  账号
	 * @param PamAccount $item 账号
	 * @return bool
	 */
	public function disable(PamAccount $pam, PamAccount $item)
	{
		// 不得禁用自身
		if ($pam->id === $item->id) {
			return false;
		}

		return !$this->enable($pam, $item);
	}
}