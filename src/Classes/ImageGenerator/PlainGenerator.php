<?php namespace Poppy\System\Classes\ImageGenerator;

use Intervention\Image\AbstractFont;
use Intervention\Image\Gd\Font;
use Intervention\Image\Image;

/**
 * 生成基本文字
 */
class PlainGenerator extends BaseImageGenerator
{
	/**
	 * 生成图片
	 * @param $width
	 * @param $height
	 * @param $text
	 * @return Image|void
	 */
	public function gen($width, $height, $text)
	{
		$img      = $this->manager->canvas($width, $height, '#ccc');
		$fontFile = poppy_path('poppy.system', 'resources/fonts/fzzzhjt.ttf');

		// min: 20 /max 50
		$size = (($width / 10) <= 20)
			? 20
			: (($width / 10) >= 50 ? 50 : round($width / 10));

		// write size
		$sizeFont = new Font();
		$sizeFont->text("{$width}x{$height}");
		$sizeFont->size($size);
		$sizeFont->file($fontFile);
		$box        = $sizeFont->getBoxSize();
		$fontHeight = $box['height'];
		$fontWidth  = $box['width'];
		$y          = ($height - $fontHeight) / 2 + $fontHeight;
		$x          = ($width - $fontWidth) / 2;
		$img->text("{$width}x{$height}", $x, $y, function (AbstractFont $font) use ($fontFile, $size) {
			$font->align('left');
			$font->color('#999');
			$font->size($size);
			$font->file($fontFile);
		});

		// write desc
		if ($text) {
			$descFont = new Font();
			$descFont->text($text);
			$descFont->size(round($size * .85));
			$descFont->file($fontFile);
			$box       = $descFont->getBoxSize();
			$fontWidth = $box['width'];
			$y         = $height - 5;
			$x         = $width - $fontWidth - 5;
			$img->text($text, $x, $y, function (AbstractFont $font) use ($fontFile, $size) {
				$font->align('left');
				$font->color('#888');
				$font->size($size * .85);
				$font->file($fontFile);
			});
		}
		return $img;
	}
}
