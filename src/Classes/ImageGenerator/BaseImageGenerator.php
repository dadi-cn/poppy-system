<?php namespace Poppy\System\Classes\ImageGenerator;

use Intervention\Image\ImageManager;

/**
 * 图形生成器
 */
abstract class BaseImageGenerator
{
	protected $manager;

	public function __construct()
	{
		$this->manager = new ImageManager();
	}

	public function gen($width, $height, $text)
	{
	}
}
