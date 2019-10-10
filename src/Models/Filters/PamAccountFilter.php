<?php namespace Poppy\System\Models\Filters;

use EloquentFilter\ModelFilter;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRoleAccount;
use User\Models\UserProfile;

/**
 * 用户filter
 */
class PamAccountFilter extends ModelFilter
{
	/**
	 * 根据类型查询
	 * @param string $type 类型
	 * @return PamAccountFilter
	 */
	public function type($type)
	{
		return $this->where('type', $type);
	}

	/**
	 * 根据ID查询
	 * @param int $id 用户id
	 * @return PamAccountFilter
	 */
	public function id($id)
	{
		return $this->where('id', $id);
	}

	/**
	 * 根据用户ID/手机/邮箱/用户名查询
	 * @param string $passport 通行证
	 * @return PamAccountFilter
	 */
	public function passport($passport)
	{
		$pam = PamAccount::passport($passport);
		if (!$pam) {
			return $this->whereRaw('1=0');
		}

		return $this->where('id', $pam->id);
	}

	/**
	 * 返回父级id
	 * @param int $id id
	 * @return PamAccountFilter
	 */
	public function parent($id)
	{
		return $this->where('parent_id', $id);
	}

	/**
	 * 返回父级昵称
	 * @param string $nickname 昵称
	 * @return PamAccountFilter
	 */
	public function parentNickname($nickname)
	{
		$profile = UserProfile::where('nickname', $nickname)->first();
		if ($profile) {
			return $this->where('parent_id', $profile->account_id);
		}

		return $this->whereRaw('1=0');
	}

	/**
	 * 根据昵称返回id
	 * @param string $nickname 昵称
	 * @return PamAccountFilter
	 */
	public function nickname($nickname)
	{
		$profile = UserProfile::where('nickname', $nickname)->first();
		if ($profile) {
			return $this->where('id', $profile->account_id);
		}

		return $this->whereRaw('1=0');
	}

	/**
	 * 根据角色ID查询
	 * @param int $role_id 角色id
	 * @return PamAccountFilter
	 */
	public function role($role_id)
	{
		$account_ids = PamRoleAccount::where('role_id', $role_id)->pluck('account_id');

		return $this->whereIn('id', $account_ids);
	}
}