<?php namespace Poppy\System\Http\Middlewares\Backend;

use Closure;
use Illuminate\Http\Request;
use Poppy\System\Models\PamAccount;
use View;

/**
 * 登录成功后之后向 view 中附加数据
 */
class AppendData
{

	/**
	 * Handle an incoming request.
	 * @param  Request $request 请求
	 * @param  Closure $next    后续处理
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		/** @var PamAccount $pam */
		$pam = $request->user();
		if (!sys_is_pjax()) {
			View::share([
				'_menus' => app('module')->menus()->withPermission($pam),
			]);
		}
		View::share([
			'_pam' => $pam,
		]);

		return $next($request);
	}
}