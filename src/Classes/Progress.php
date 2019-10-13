<?php namespace Poppy\System\Classes;

use Illuminate\Support\Str;
use Poppy\Framework\Helper\FileHelper;

/**
 * 数据库更新读取
 */
class Progress
{
	/**
	 * 全部类名
	 * @var array $all
	 */
	private static $all = [];

	/**
	 * 已经执行过的类名
	 * @var array $already
	 */
	private static $already = [];

	/**
	 * 数据库更新读取
	 * @return mixed
	 */
	public static function handle()
	{
		// 读取每个模块 找到 progress 文件下的 每个类
		app('poppy')->enabled()->pluck('slug')->map(function ($item) {
			$path = base_path('modules/' . str_replace('.', '/', $item) . '/src/progress/');
			if (FileHelper::isDir($path)) {
				return [
					'path'   => $path,
					'module' => $item,
				];
			}

			return '';
		})->filter()->each(function ($item) use (&$progresses) {
			$files = FileHelper::listFile($item['path']);
			foreach ($files as $file) {
				$name        = FileHelper::removeExtension($file);
				$class       = Str::snake(substr($name, strrpos($name, '/') + 1));
				$cache_class = sys_setting('system::progress.' . Str::snake($class)) ?? [];
				// 获取 sys_setting 中 已经执行过的类 进行对比 返回 执行过的类
				if ($class === $cache_class) {
					$progresses[]    = $class;
					self::$already[] = $class;
				}

				self::$all[] = [
					'class'  => $class,
					'module' => $item['module'],
				];
			}
		});

		return $progresses ?? [];
	}

	/**
	 * 获取全部的类
	 * @return array
	 */
	public static function getAll(): array
	{
		return self::$all;
	}

	/**
	 * 获取已经执行过的类
	 * @return array
	 */
	public static function getAlready(): array
	{
		return self::$already;
	}
}
