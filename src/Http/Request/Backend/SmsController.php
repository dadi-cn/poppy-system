<?php namespace Poppy\System\Http\Request\Backend;

use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Sms;
use Poppy\System\Models\PamAccount;

/**
 * 短信控制器
 */
class SmsController extends BackendController
{

	public function __construct()
	{
		parent::__construct();

		self::$permission = [
			'global' => 'backend:system.global.manage',
		];
	}

	/**
	 * @return Factory|View
	 */
	public function index()
	{
		return view('poppy-system::backend.sms.index', [
			'items' => array_values($this->action()->getTemplates()),
		]);
	}

	/**
	 * 短信模板c2e
	 * @param null|int $id
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function establish($id = null)
	{
		$Sms = $this->action();
		if (is_post()) {
			if (!$Sms->establish(input(), $id)) {
				return Resp::web(Resp::ERROR, $Sms->getError());
			}

			return Resp::web(Resp::SUCCESS, '操作成功!~', 'reload_opener|1');
		}

		if ($id) {
			$Sms->init($id) && $Sms->share();
		}
		return view('poppy-system::backend.sms.establish');
	}

	/**
	 * 删除短信模板
	 * @param null|int $id id
	 * @return JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function destroy($id = null)
	{
		$Sms = $this->action();
		if (!$Sms->destroy($id)) {
			return Resp::web(Resp::ERROR, $Sms->getError());
		}

		return Resp::web(Resp::SUCCESS, '操作成功', 'reload|1');
	}


	/**
	 * @return Sms
	 */
	private function action(): Sms
	{
		return (new Sms())->setPam(Auth::guard(PamAccount::GUARD_BACKEND)->user());
	}
}