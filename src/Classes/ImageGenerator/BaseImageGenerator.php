<?php namespace Poppy\System\Classes\ImageGenerator;

use Intervention\Image\ImageManager;
use Poppy\System\Classes\Contracts\Image\Generator;

/**
 * kelas dasar untuk image generator
 *
 * @author Zamrony P. Juhara
 */
abstract class BaseImageGenerator implements Generator
{
	protected $manager;

	public function __construct()
	{
		$this->manager = new ImageManager();
	}
}
