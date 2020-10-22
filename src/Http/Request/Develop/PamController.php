<?php namespace Poppy\System\Http\Request\Develop;

use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;

/**
 * 开发平台用户登录控制器
 */
class PamController extends DevelopController
{
	/**
	 * 登录
	 * @param Request $request 请求
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function login(Request $request)
	{
		if (is_post()) {
			$username = $request->input('username');
			$password = $request->input('password');

			/** @var Pam $pam */
			$pam = new Pam();
			if ($pam->loginCheck($username, $password, PamAccount::GUARD_DEVELOP, true)) {
				return Resp::success('登录成功！', 'location|' . route('system:develop.cp.cp'));
			}

			return Resp::error($pam->getError());
		}
		$guard = Auth::guard(PamAccount::GUARD_DEVELOP)->user();
		// todo check guard permission
		if ($guard) {
			return Resp::success('您已登录', [
				'location' => route('system:develop.cp.cp'),
			]);
		}

		return view('poppy-system::develop.pam.login');
	}

	public function logout()
	{
		$guard = Auth::guard(PamAccount::GUARD_DEVELOP);
		if ($guard->user()) {
			$guard->logout();
		}
		return Resp::success('退出登录成功', [
			'location' => route('system:develop.pam.login'),
		]);
	}
}