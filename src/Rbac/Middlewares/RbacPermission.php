<?php namespace Poppy\System\Rbac\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Route;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

/**
 * 用户权限
 */
class RbacPermission
{
	/**
	 * Handle an incoming request.
	 * @param Request $request 请求
	 * @param Closure $next    后续处理
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		/** @var PamAccount $user */
		$user = $request->user();

		$controller = Route::current()->controller;

		/* 未定义权限, 通过
		 * ---------------------------------------- */
		if (!($controller::$permission ?? '')) {
			return $next($request);
		}

		/* 超级管理员通过
		 * ---------------------------------------- */
		if ($user->type === PamAccount::TYPE_BACKEND && $user->hasRole(PamRole::BE_ROOT)) {
			return $next($request);
		}

		$permissions = $controller::$permission;

		/* 存在方法权限, 不验证 global
		 * ---------------------------------------- */
		$method           = Str::after(Route::currentRouteAction(), '@');
		$methodPermission = $permissions[$method] ?? '';
		if ($methodPermission && app('permission')->has($methodPermission)) {
			if ($user->capable($methodPermission)) {
				return $next($request);
			}

			return Resp::error('用户方法权限访问受限');
		}

		/* 全局权限
		 * ---------------------------------------- */
		$globalPermission = $permissions['global'] ?? '';
		if ($globalPermission && app('permission')->has($globalPermission)) {
			if ($user->capable($globalPermission)) {
				return $next($request);
			}
			return Resp::error('用户权限访问受限');
		}
		return $next($request);
	}
}
