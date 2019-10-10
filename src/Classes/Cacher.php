<?php namespace Poppy\System\Classes;


use Carbon\Carbon;
use Closure;

/**
 * 缓存模拟器
 */
class Cacher
{
	/**
	 * 缓存器, 随机秒数缓存器, 不在同一时刻读取值
	 * @param string $key    键
	 * @param mixed  $value  值
	 * @param int    $second 秒数
	 * @return mixed
	 */
	public static function seconds($key, $value, $second = 30)
	{
		$cacheData = [
			'expired' => Carbon::now()->addSecond($second)->timestamp,
		];
		$cacherKey = 'system.classes.cacher.' . $key;
		$fetchData = sys_cache('system')->get($cacherKey);
		// 无数据 / 已过期
		if (!$fetchData || $fetchData['expired'] < Carbon::now()->timestamp) {
			if ($value instanceof Closure) {
				$value = $value();
			}
			$cacheData['value'] = $value;
			sys_cache('system')->forever($cacherKey, $cacheData);

			return $cacheData['value'];
		}

		return $fetchData['value'];
	}
}
