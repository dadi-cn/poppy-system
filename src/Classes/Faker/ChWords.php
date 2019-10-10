<?php namespace Poppy\System\Classes\Faker;

use Exception;

/**
 * 生成随机中文文字
 */
class ChWords
{
	/**
	 * 随机生成N个汉字
	 * @param  int $num 需要生成多少个汉字
	 * @return string 返回生成的字符串
	 */
	public static function create($num = 12)
	{
		try {
			$zh = '';
			for ($i = 0; $i < $num; $i++) {
				$zh .= '&#' . random_int(19968, 40869) . ';';
			}

			return mb_convert_encoding($zh, 'UTF-8', 'HTML-ENTITIES');
		} catch (Exception $e) {
			return '';
		}
	}
}