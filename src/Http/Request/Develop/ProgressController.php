<?php namespace Poppy\System\Http\Request\Develop;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Classes\Progress;
use Poppy\System\Classes\Traits\FixTrait;

/**
 * 更新数据
 */
class ProgressController extends DevelopController
{
	use FixTrait;

	public function lists()
	{
		Progress::handle();

		return view('system::develop.progress.lists', [
			'all'     => Progress::getAll(),
			'already' => Progress::getAlready(),
		]);
	}

	/**
	 * 展示更新进度
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function index()
	{
		$method = strtolower(input('method'));

		if (!$method) {
			return Resp::error('请填写执行参数');
		}

		list($module, $class_name) = explode('.', $method);
		if (!app('module')->pages()->offsetExists('module.' . $module)) {
			return Resp::error('模型不存在');
		}

		$class = '\\' . ucfirst($module) . '\\Progress\\' . Str::studly($class_name);
		if (!class_exists($class)) {
			return Resp::error('类不存在');
		}

		if (in_array($class_name, Progress::handle(), true)) {
			return Resp::error($class_name . ' 已更新');
		}

		$this->fix = (new $class())->handle();

		$this->fix['title']  = $class;
		$this->fix['method'] = $method;

		if ($this->fix['left'] === 0) {
			app('setting')->set('system::progress.' . Str::snake($class_name), $class_name);
		}

		return $this->fixView();
	}
}
