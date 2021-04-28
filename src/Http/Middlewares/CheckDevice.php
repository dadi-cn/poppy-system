<?php

namespace Poppy\System\Http\Middlewares;

use Carbon\Carbon;
use Closure;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\System\Models\PamBan;
use Poppy\System\Models\PamToken;

class CheckDevice
{

    /**
     * @var PamToken
     */
    public static $init;

    /**
     * @param         $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //获取ip
        $ip = EnvHelper::ip();
        if (!PamBan::ipIsAllow($ip)) {
            return Resp::error('当前ip被封禁，请联系客服处理');
        }

        //获取token
        $token = input('token');
        if ($token && !PamToken::getInstance()) {
            $token_hash = PamToken::where('token_hash', md5(input('token')))->first();
            if (!$token_hash) {
                return Resp::error('token失效,无法获取用户信息');
            }
            // 更新token时间
            PamToken::where('token_hash', md5(input('token')))->update([
                'expired_at' => Carbon::now()->addHour(),
            ]);
            PamToken::setInstance($token_hash);
        }

        // 缓存

        //
        //获取当前设备ID
        $init = PamToken::getInstance();
        if ($init && !PamBan::deviceIsAllow($init->device_id)) {
            return Resp::error('当前设备被封禁，请联系客服处理');
        }

        return $next($request);
    }
}