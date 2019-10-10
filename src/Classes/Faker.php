<?php namespace Poppy\System\Classes;

use Poppy\System\Classes\Faker\Chid;
use Poppy\System\Classes\Faker\ChWords;
use Throwable;

/**
 * 身份证号生成器的调用
 */
class Faker
{
	/**
	 * 返回正确的身份证号
	 * @return string
	 */
	public static function chid(): string
	{
		try {
			return Chid::create();
		} catch (Throwable $e) {
			return '';
		}
	}

	/**
	 * 返回随机中文汉字
	 * @param int $num 生成数量
	 * @return string
	 */
	public static function chWords($num = 8): string
	{
		return ChWords::create($num);
	}
}