<?php namespace Poppy\System\Http\Request\Web;

use Illuminate\Http\Response;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Poppy\System\Classes\ImageGenerator\RandomBlurMozaicGenerator;

/**
 * 占位符
 */
class PlaceholderController extends Controller
{
	use ViewTrait;

	public function index()
	{
		return view('system::web.place_holder.index');
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return Response
	 */
	public function image($width = 100, $height = 100)
	{
		return $this->generateImage($width, $height, 'png');
	}

	private function generateImage($width, $height, $format)
	{
		$img = (new RandomBlurMozaicGenerator())->generate($width, $height);

		return $img->response($format);
	}
}
