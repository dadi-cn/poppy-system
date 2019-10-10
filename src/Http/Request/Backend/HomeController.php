<?php namespace Poppy\System\Http\Request\Backend;

use Auth;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Pam;
use Poppy\System\Classes\Ui\ListUi;
use Poppy\System\Classes\Ui\PageUi;
use Poppy\System\Classes\Ui\SettingUI;
use Poppy\System\Models\Filters\PamAccountFilter;
use Poppy\System\Models\PamAccount;
use Throwable;
use Validator;

/**
 * 主页控制器
 */
class HomeController extends BackendController
{
	/**
	 * 主页
	 * @return View
	 */
	public function index()
	{
		return view('system::backend.home.index');
	}

	/**
	 * 登录
	 * @return RedirectResponse|Redirector
	 * @internal param LoginRequest $request
	 */
	public function login()
	{
		$auth = $this->auth();
		if (is_post()) {
			$Pam = new Pam();
			if ($Pam->loginCheck(input('username'), input('password'), PamAccount::GUARD_BACKEND)) {
				$auth->login($Pam->getPam(), true);
				return Resp::web(Resp::SUCCESS, '登录成功', 'location|' . route('system:backend.home.index'));
			}
			return Resp::web(Resp::ERROR, $Pam->getError());
		}

		if ($auth->check()) {
			return Resp::web(Resp::SUCCESS, '登录成功', 'location|' . route('system:backend.home.index'));
		}

		return view('system::backend.home.login');
	}

	/**
	 * 修改本账户密码
	 * todo
	 * @param Request $request 请求
	 * @return RedirectResponse|Redirector
	 */
	public function password(Request $request)
	{
		if (is_post()) {
			$old_password = $request->input('old_password');
			$password     = trim($request->input('password'));
			$validator    = Validator::make($request->all(), [
				'password'     => 'required|confirmed',
				'old_password' => 'required',
			]);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->errors());
			}

			$pam = $this->pam();
			$Pam = new Pam();
			if (!$Pam->password()->check($pam, $old_password)) {
				return Resp::web(Resp::ERROR, '原密码错误!');
			}

			$Pam->setPassword($pam, $password);
			$this->auth()->logout();

			return Resp::web(Resp::SUCCESS, '密码修改成功, 请重新登录', 'location|' . route('system:backend.home.login'));
		}

		return view('system::backend.home.password');
	}

	/**
	 * 后台前端帮助文件
	 * @param $param
	 * @return array|Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function fe($param = null)
	{
		if ($param) {
			if (is_post()) {
				return Resp::success('提交信息成功', 'top_reload|1');
			}

			return view('system::backend.home.fe-' . $param);
		}
		try {
			$random = random_int(0, 9999);
		} catch (Exception $e) {
			$random = 0;
		}

		return view('system::backend.home.fe', compact('random'));
	}


	/**
	 * 页面渲染
	 * @return array|Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function samplePage()
	{
		try {
			$Page = new PageUi('system.sample');
			return $Page->render();
		} catch (Exception $e) {
			return Resp::error($e->getMessage());
		}
	}

	/**
	 * 示例列表
	 * @return array|Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function sampleList()
	{
		try {
			$input         = input();
			$type          = sys_get($input, 'type', PamAccount::TYPE_BACKEND);
			$input['type'] = $type;
			$types         = PamAccount::kvType();
			$items         = PamAccount::filter($input, PamAccountFilter::class)->paginateFilter($this->pagesize);

			$Page = new ListUi('system.sample');
			$Page->withDefinition($input, $this->pagesize);
			return $Page->render();
		} catch (Exception $e) {
			return Resp::error($e->getMessage());
		}
	}

	/**
	 * 登出
	 * @return RedirectResponse|Redirector
	 */
	public function logout()
	{
		Auth::guard(PamAccount::GUARD_BACKEND)->logout();

		return Resp::web(Resp::SUCCESS, '退出登录', 'location|' . route('system:backend.home.login'));
	}

	/**
	 * 控制面板
	 * @return View
	 */
	public function cp()
	{
		return view('system::backend.home.cp');
	}

	/**
	 * Setting
	 * @param Request $request request
	 * @param string  $path    地址
	 * @return mixed
	 */
	public function setting(Request $request, $path = 'module.system')
	{
		try {
			$Setting = (new SettingUI($path))->setPam($this->pam());
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

	/**
	 * tools
	 * @param null|string $type 类型
	 * @return Factory|View
	 */
	public function easyWeb($type = null)
	{

		return view('system::backend.home.easyweb.' . $type);
	}

	/**
	 * 获取后台的Auth
	 * @return Guard|StatefulGuard
	 */
	private function auth()
	{
		return Auth::guard(PamAccount::GUARD_BACKEND);
	}
}