<?php namespace Poppy\System\Http\Request\Web;

use Illuminate\Http\Response;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Poppy\System\Classes\ImageGenerator\PlainGenerator;

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
	 * @param int    $spec
	 * @param string $text
	 * @return Response
	 */
	public function image($spec = 50, $text = '')
	{
		return $this->generateImage($spec, $text);
	}

	private function generateImage($spec, $text)
	{
		$width = $height = 0;
		if (is_numeric($spec)) {
			$width = $height = $spec;
		}
		if (strpos($spec, 'x') !== false) {
			[$width, $height] = explode('x', $spec);
			$width  = (int) $width;
			$height = (int) $height;
			if (!$height) {
				$height = $width;
			}
		}
		$img = (new PlainGenerator())->gen($width, $height, $text);

		return $img->response('png');
	}
}
