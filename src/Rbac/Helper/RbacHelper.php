<?php namespace Poppy\System\Rbac\Helper;

use Illuminate\Database\Eloquent\Collection;
use Poppy\System\Models\PamPermission;

/**
 * Class RbacHelper
 */
class RbacHelper
{
	/**
	 * 获取权限以及分组
	 * @param string $account_type 账号类型
	 * @return Collection
	 */
	public static function permission($account_type)
	{
		$permission = PamPermission::where('account_type', $account_type)->get();
		$collection = new Collection($permission);

		return $collection->groupBy('permission_group');
	}
}

