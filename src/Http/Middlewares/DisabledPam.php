<?php namespace Poppy\System\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\SysConfig;
use Response;
use Tymon\JWTAuth\JWTGuard;

/**
 * 用户禁用不可访问
 */
class DisabledPam
{

    /**
     * Handle an incoming request.
     * @param Request $request 请求
     * @param Closure $next    后续处理
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var JWTGuard $guard */
        $guard = app('auth')->guard();
        if ($guard->check()) {
            /** @var PamAccount $user */
            $user = $guard->user();
            if ($user->is_enable === SysConfig::NO) {
                $defaultReason = '用户被禁用, 原因 : ' . $user->disable_reason . ', 解禁时间 : ' . $user->disable_end_at;
                if ($disableReason = sys_setting('py-system::pam.disable_reason')) {
                    $defaultReason = $disableReason;
                }
                return Response::make($defaultReason, 401);
            }
        }

        return $next($request);
    }
}