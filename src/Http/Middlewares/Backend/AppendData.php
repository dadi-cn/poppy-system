<?php namespace Poppy\System\Http\Middlewares\Backend;

use Closure;
use Illuminate\Http\Request;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Core\Exceptions\PermissionException;
use Poppy\Core\Module\ModuleManager;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use View;

/**
 * 登录成功后之后向 view 中附加数据
 */
class AppendData
{
	use CoreTrait;

	/**
	 * Handle an incoming request.
	 * @param Request $request 请求
	 * @param Closure $next    后续处理
	 * @return mixed
	 * @throws PermissionException
	 */
	public function handle($request, Closure $next)
	{
		/** @var PamAccount $pam */
		$pam = $request->user();
		if (!sys_is_pjax()) {
			$isFullPermission = $pam->hasRole(PamRole::BE_ROOT);
			View::share([
				'_menus' => $this->coreModule()->menus()->withPermission(PamAccount::TYPE_BACKEND, $isFullPermission, $pam),
			]);
		}
		View::share([
			'_pam' => $pam,
		]);

		return $next($request);
	}
}