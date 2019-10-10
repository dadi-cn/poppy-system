<?php namespace Poppy\System\Models\Filters;

use EloquentFilter\ModelFilter;

/**
 * 登录日志filter
 */
class PamLogFilter extends ModelFilter
{
	/**
	 * 用户ID
	 * @param int $account 用户ID
	 * @return PamLogFilter
	 */
	public function account($account): self
	{
		return $this->where('account_id', $account);
	}

	/**
	 * IP地址
	 * @param string $ip IP
	 * @return PamLogFilter
	 */
	public function ip($ip): self
	{
		return $this->where('ip', $ip);
	}

	/**
	 * 所在地
	 * @param string $area 所在地
	 * @return PamLogFilter
	 */
	public function area($area): self
	{
		return $this->where('area_text', 'like', '%' . $area . '%');
	}
}