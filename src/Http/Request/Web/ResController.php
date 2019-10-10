<?php namespace Poppy\System\Http\Request\Web;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Response;
use Throwable;

/**
 * 后端入口
 */
class ResController extends Controller
{
	use ViewTrait;

	/**
	 * 例如
	 * fe:js.centrifuge.centrifuge-min-js
	 * 代表 fe 模块 js/centrifuge 文件夹下
	 * centrifuge.min.js
	 * @param string $key 模块名称
	 * @return \Illuminate\Http\Response
	 */
	public function mix($key = null)
	{
		[$module, $key] = explode(':', $key);

		$module = $module ?? 'system';
		$key    = $key ?? 'web-demo';
		$dirs   = 'resources/' . str_replace(['.', '-'], ['/', '.'], $key);
		try {
			$file = poppy_path($module, $dirs);
			if (file_exists($file)) {
				return Response::make(app('files')->get($file));
			}
		} catch (Throwable $e) {
			return Response::make('路径不正确');
		}
	}

	/**
	 * 返回示例json 数据
	 */
	public function translate()
	{
		return Resp::success('翻译信息', [
			'json'         => true,
			'translations' => app('translator')->fetch('zh'),
		]);
	}
}
