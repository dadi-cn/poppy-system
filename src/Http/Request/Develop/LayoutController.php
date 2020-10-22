<?php namespace Poppy\System\Http\Request\Develop;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;

/**
 * 基础布局文件
 */
class LayoutController extends DevelopController
{
	public function __construct()
	{
		parent::__construct();
		\View::share([
			'faker' => \Faker\Factory::create(),
		]);
	}

	/**
	 * @param string $page 需要引入的页面地址
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function index($page = '')
	{
		if (!$page) {
			$page = 'index';
		}
		try {
			return view($page);
		} catch (Exception $e) {
			return Resp::error('文件 `' . $page . '.blade.php` 在 `~/resources/views/` 目录下不存在!');
		}
	}

	/**
	 * 邮件样式预览
	 * @param string $slug 模块
	 * @param string $page 页面
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function mail($slug = 'system', $page = 'test')
	{
		try {
			/** @var Mailable $class */
			$class = poppy_class('system', 'Mail\\' . Str::studly($page) . 'Mail');

			return (new $class())->render();
		} catch (Exception $e) {
			return Resp::error('文件 `' . $page . '.blade.php` 在 `~/modules/' . $slug . '/resources/views/email/` 目录下不存在!');
		}
	}

	/**
	 * 前台代码
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function fe()
	{
		if (is_post()) {
			$type = input('type');
			if ($type === 'submit') {
				return Resp::success('J_submit 提交, title:' . input('title'));
			}
			if ($type === 'validate') {
				return Resp::success('J_validate 提交, title:' . input('title'));
			}

			return Resp::success('J_request 请求测试');
		}

		return view('poppy-system::develop.layout.fe', [
			'pam' => $this->pam(),
		]);
	}
}