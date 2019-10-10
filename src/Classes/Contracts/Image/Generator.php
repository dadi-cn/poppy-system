<?php namespace Poppy\System\Classes\Contracts\Image;

/**
 * Interface untuk image generator
 * @author Zamrony P. Juhara
 */
interface Generator
{
	/**
	 * Membuat image berdasarkan panjang dan lebar
	 * yang ditentukan oleh parameter
	 * @param int $width
	 * @param int $height
	 * @return Intervention\Image\Image
	 */
	public function generate($width, $height);
}
