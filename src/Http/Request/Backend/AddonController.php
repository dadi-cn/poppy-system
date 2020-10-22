<?php namespace Poppy\System\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Classes\Ui\SettingUI;
use Throwable;

/**
 * 扩展控制器
 */
class AddonController extends BackendController
{
	/**
	 * @var bool
	 */
	protected $onlyValues = true;

	public function __construct()
	{
		parent::__construct();
		self::$permission = [
			'global' => 'backend:system.global.addon',
		];
	}

	/**
	 * 列表
	 * @return array|Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function index()
	{
		try {
			$manifest = app('files')->glob(base_path('addons/*/*/manifest.json'));
			$items    = [];
			if (count($manifest)) {
				foreach ($manifest as $mani) {
					$content                   = json_decode(app('files')->get($mani), true);
					$items[$content['folder']] = $content;
				}
			}
		} catch (Throwable $e) {
			return Resp::error($e->getMessage());
		}

		return view('poppy-system::backend.addon.index', [
			'items' => $items,
		]);
	}

	/**
	 * 配置
	 * @param Request $request 请求
	 * @param string  $folder  文件夹名称
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function config(Request $request, $folder)
	{
		try {
			$Setting = (new SettingUI('addon.' . $folder))->setPam($this->pam());
			if (is_post()) {
				if (!$Setting->save($request)) {
					return Resp::web(Resp::ERROR, $Setting->getError(), 'forget|1');
				}

				return Resp::web(Resp::SUCCESS, '更新配置成功', 'forget|!');
			}

			return $Setting->render();
		} catch (Throwable $e) {
			return Resp::web(Resp::ERROR, $e->getMessage());
		}
	}
}
