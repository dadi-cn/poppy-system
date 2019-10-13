<?php namespace Poppy\System\Http\Middlewares;

use Closure;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Models\PamAccount;
use Route;

/**
 * Class Authenticate.
 */
class Authenticate extends IlluminateAuthenticate
{

	/**
	 * 授权
	 * @param Request $request
	 * @param array   $guards 提供的保护伞
	 * @return mixed
	 * @throws AuthenticationException
	 */
	protected function authenticate($request, array $guards)
	{
		if (empty($guards)) {
			return app('auth')->authenticate();
		}
		foreach ($guards as $guard) {
			if (app('auth')->guard($guard)->check()) {
				return app('auth')->shouldUse($guard);
			}
		}
		throw new AuthenticationException('Unauthenticated.', $guards);
	}

	/**
	 * Handle an incoming request.
	 * @param Request $request 请求
	 * @param Closure $next    后续处理
	 * @param array   $guards  可以支持的 guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, ...$guards)
	{
		try {
			$this->authenticate($request, $guards);

		} catch (Exception $e) {
			if ($request->expectsJson()) {
				return response()->json([
					'message' => 'Unauthorized',
				], 401);
			}
			// develop
			if (in_array(PamAccount::GUARD_DEVELOP, $guards, true)) {
				return Resp::error('无权限访问', 'location|' . route('system:develop.pam.login'));
			}
			// backend
			if (in_array(PamAccount::GUARD_BACKEND, $guards, true)) {
				return Resp::error('无权限访问', 'location|' . route('system:backend.home.login'));
			}
			$isWeb = in_array(PamAccount::GUARD_WEB, $guards, true);
			if ($isWeb || in_array(PamAccount::GUARD_JWT_WEB, $guards, true)) {
				$route = Route::currentRouteName();
				if ($route && Str::contains($route, ':mobile.') && config('poppy.guard_location.mobile')) {
					$appends = [
						'location' => route_url(config('poppy.guard_location.mobile'), [], [
							'go' => route_url($route),
						]),
					];
					return Resp::error('无权限访问', $appends);
				}
				if ($isWeb && config('poppy.guard_location.web')) {
					$appends = [
						'location' => route(config('poppy.guard_location.web')),
					];
					return Resp::error('无权限访问', $appends);
				}
			}

			return response('Unauthorized.', 401);
		}
		return $next($request);
	}
}