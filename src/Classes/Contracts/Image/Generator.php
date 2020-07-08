<?php namespace Poppy\System\Classes\Contracts\Image;

use Intervention\Image\Image;

/**
 * Interface untuk image generator
 */
interface Generator
{
	/**
	 * 生成文件
	 * @param int $width
	 * @param int $height
	 * @return Image
	 */
	public function generate($width, $height);
}
