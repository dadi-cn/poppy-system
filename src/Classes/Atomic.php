<?php namespace Poppy\System\Classes;

use Cache;
use Carbon\Carbon;
use Predis\Client;

class Atomic
{
	/**
	 * 原子性鉴定, 这个必须是 Redis 缓存才可生效
	 * @param string $key     key
	 * @param int    $seconds 秒数
	 * @return bool
	 */
	public static function inLock($key, $seconds): bool
	{
		/* 非 Redis 不进行原子性鉴定
		 * ---------------------------------------- */
		if (strtolower(config('cache.default')) !== 'redis') {
			return true;
		}
		$prefix = config('cache.prefix');
		$key    = $prefix . ':atomic_lock:' . $key;
		if (strtolower(env('CACHE_DRIVER')) === 'redis') {
			$client = new Client(config('database.redis.default'));
			$status = $client->set($key, 'atomic_' . Carbon::now()->timestamp, 'EX', $seconds, 'NX');

			return null === $status;
		}
		$now = Carbon::now()->timestamp;
		if (Cache::has($key)) {
			$content = Cache::get($key);
			if ($content < $now) {
				Cache::forget($key);

				return true;
			}

			return false;
		}
		Cache::forever($key, $now + $seconds);

		return true;
	}
}
