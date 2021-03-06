<?php

namespace Poppy\System\Classes\Traits;

/**
 * Account 过滤
 */
trait FieldFilterTrait
{
	/**
	 * @param int $id 用户id
	 * @return mixed
	 */
	public function account($id)
	{
		return $this->where('account_id', $id);
	}
}