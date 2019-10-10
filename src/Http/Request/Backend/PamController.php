<?php namespace Poppy\System\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\System\Models\Filters\PamAccountFilter;
use Poppy\System\Models\Filters\PamLogFilter;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;
use Poppy\System\Models\PamRole;
use Validator;

/**
 * 账户管理
 */
class PamController extends BackendController
{
	public function __construct()
	{
		parent::__construct();

		self::$permission = [
			'global' => 'backend:system.pam.manage',
			'log'    => 'backend:system.pam.log',
		];
	}

	/**
	 * Display a listing of the resource.
	 * @return \Response
	 */
	public function index()
	{
		$input         = input();
		$type          = sys_get($input, 'type', PamAccount::TYPE_BACKEND);
		$input['type'] = $type;
		$types         = PamAccount::kvType();
		$items         = PamAccount::filter($input, PamAccountFilter::class)->paginateFilter($this->pagesize);

		return view('system::backend.pam.index', [
			'items' => $items,
			'type'  => $type,
			'types' => $types,
			'roles' => PamRole::getLinear($type),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 * @param null|int $id ID
	 * @return JsonResponse|RedirectResponse|Response|Redirector|\Response
	 */
	public function establish($id = null)
	{
		$username  = input('username');
		$password  = input('password');
		$role_name = input('role_name');

		if (!$role_name && is_post()) {
			return Resp::web(Resp::ERROR, '请选择角色');
		}
		if ($id) {
			/** @var PamAccount $item */
			$item = PamAccount::passport($id);
			if (!$item) {
				return Resp::web(Resp::ERROR, '无此用户');
			}
			\View::share('item', $item);
			\View::share('item_roles', $item->roles->pluck('name'));
			$type = $item->type;

			if (is_post()) {
				$Pam = new Pam();
				if ($password) {
					$Pam->setPassword($item, $password);
				}
				$Pam->setRoles($item, $role_name);

				return Resp::web(Resp::SUCCESS, '用户修改成功', 'top_reload|1');
			}
		}
		else {
			$type = input('type');
			if (is_post()) {
				$Pam = new Pam();
				if ($Pam->register($username, $password, $role_name)) {
					return Resp::web(Resp::SUCCESS, '用户添加成功', 'top_reload|1');
				}

				return Resp::web(Resp::ERROR, $Pam->getError());
			}
		}

		return view('system::backend.pam.establish', [
			'type'  => $type,
			'roles' => PamRole::getLinear($type, 'name'),
		]);
	}

	/**
	 * 设置密码
	 * @param int $id 用户ID
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function password($id)
	{
		$pam = PamAccount::find($id);
		if (is_post()) {
			$validator = Validator::make(input(), [
				'password' => [
					Rule::required(),
					Rule::confirmed(),
				],
			], []);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->errors());
			}

			$password = input('password');

			/** @var Pam $actPam */
			$actPam = new Pam();
			$actPam->setPam($this->pam());
			if ($actPam->setPassword($pam, $password)) {
				return Resp::web(Resp::SUCCESS, '设置密码成功', 'top_reload|1');
			}

			return Resp::web(Resp::ERROR, $actPam->getError());
		}

		return view('system::backend.pam.password', [
			'pam' => $pam,
		]);
	}

	/**
	 * 禁用用户
	 * @param int $id 用户ID
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function disable($id)
	{
		if (!$id) {
			return Resp::web(Resp::ERROR, '您尚未选择用户!');
		}

		if (is_post()) {
			$date     = input('date', '');
			$reason   = input('reason', '');
			$pictures = input('pictures', []);
			$Pam      = (new Pam())->setPam($this->pam());
			if (!$Pam->disable($id, $date, $reason, $pictures)) {
				return Resp::web(Resp::ERROR, $Pam->getError());
			}

			return Resp::web(Resp::SUCCESS, '当前用户已封禁', 'pjax|1');
		}

		return view('system::backend.pam.disable', [
			'id' => $id,
		]);
	}

	/**
	 * 启用用户
	 * @param int $id 用户ID
	 * @return JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function enable($id)
	{
		if (!$id) {
			return Resp::web(Resp::ERROR, '您尚未选择用户!');
		}

		if (is_post()) {
			$Pam      = (new Pam())->setPam($this->pam());
			$reason   = input('reason', '');
			$pictures = input('pictures', []);
			if (!$Pam->enable($id, $reason, $pictures)) {
				return Resp::web(Resp::ERROR, $Pam->getError());
			}

			return Resp::web(Resp::SUCCESS, '当前用户启用', 'pjax|1');
		}

		$user = PamAccount::find($id);

		return view('system::backend.pam.enable', [
			'id'   => $id,
			'user' => $user,
		]);
	}

	/**
	 * @return View
	 */
	public function log(): View
	{
		$input = input();
		$items = PamLog::filter($input, PamLogFilter::class)
			->orderBy('id', 'desc')
			->paginate($this->pagesize)
			->appends($input);

		return view('system::backend.pam.log', [
			'items' => $items,
		]);
	}
}