<?php namespace Poppy\System\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Input;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Role;
use Poppy\System\Models\Filters\PamRoleFilter;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

/**
 * 角色管理控制器
 */
class RoleController extends BackendController
{
	public function __construct()
	{
		parent::__construct();
		$types = PamAccount::kvType();
		\View::share(compact('types'));

		self::$permission = [
			'global' => 'backend:system.role.manage',
		];
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request request
	 * @return \Response
	 */
	public function index(Request $request)
	{
		$input         = $request->all();
		$type          = $input['type'] ?? PamAccount::TYPE_BACKEND;
		$input['type'] = $type;
		$items         = PamRole::filter($input, PamRoleFilter::class)->paginateFilter();

		return view('poppy-system::backend.role.index', compact('items', 'type'));
	}

	/**
	 * 编辑 / 创建
	 * @param Request $request request
	 * @param null    $id      角色id
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function establish(Request $request, $id = null)
	{
		$Role = $this->action();
		if (is_post()) {
			if ($Role->establish($request->all(), $id)) {
				return Resp::web(Resp::SUCCESS, '创建成功', 'top_reload|1');
			}

			return Resp::web(Resp::ERROR, $Role->getError());
		}
		$id && $Role->init($id) && $Role->share();

		return view('poppy-system::backend.role.establish');
	}

	/**
	 * Remove the specified resource from storage.
	 * @param int $id 角色id
	 * @return JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function delete($id)
	{
		/** @var Role $role */
		$role = $this->action();
		if (!$role->delete($id)) {
			return Resp::web(Resp::ERROR, $role->getError());
		}

		return Resp::web(Resp::SUCCESS, '删除成功', 'top_reload|1');
	}

	/**
	 * 带单列表
	 * @param int $id 角色id
	 * @return JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function menu($id)
	{
		$role = PamRole::find($id);
		if (is_post()) {
			$perms = (array) \Request::input('permission_id');
			$Role  = $this->action();
			if (!$Role->savePermission($id, $perms)) {
				return Resp::web(Resp::SUCCESS, $Role->getError());
			}

			return Resp::web(Resp::SUCCESS, '保存会员权限配置成功!', 'reload|1');
		}
		$permission = (new Role())->permissions($id);

		if (!$permission) {
			return Resp::web(Resp::ERROR, '暂无权限信息(请检查是否初始化权限)！');
		}

		return view('poppy-system::backend.role.menu', compact('permission', 'role'));
	}

	/**
	 *
	 * @return Role
	 */
	private function action(): Role
	{
		return (new Role())->setPam($this->pam());
	}
}