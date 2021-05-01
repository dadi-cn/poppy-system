<?php

namespace Poppy\System\Listeners\LoginTokenPassed;

use Carbon\Carbon;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\System\Action\Ban;
use Poppy\System\Events\LoginTokenPassedEvent;
use Poppy\System\Events\PamSsoEvent;
use Poppy\System\Models\PamToken;

/**
 * 记录设备信息
 */
class CreateUserDeviceListener
{
    /**
     * Handle the event.
     * @param LoginTokenPassedEvent $event 用户账号
     * @return void
     * @throws ApplicationException |\Exception
     */
    public function handle(LoginTokenPassedEvent $event)
    {
        // 关闭设备登录
        if (!config('poppy.system.sso')) {
            return;
        }

        if (!$event->deviceId || !$event->deviceType) {
            throw new ApplicationException('开启单一登录必须传递设备ID/设备类型');
        }

        $tokenMd5  = md5($event->token);
        $pamId     = $event->pam->id;
        $expiredAt = Carbon::now()->addMinutes(config('jwt.ttl'));

        // 创建/更新用户的设备类型
        PamToken::updateOrInsert([
            'account_id'  => $pamId,
            'device_type' => $event->deviceType,
        ], [
            'token_hash' => $tokenMd5,
            'device_id'  => $event->deviceId,
            'expired_at' => $expiredAt->toDateTimeString(),
            'login_ip'   => EnvHelper::ip(),
        ]);

        $Ban = new Ban();
        $Ban->allow($event->pam->id, $tokenMd5, $expiredAt);

        $logoutUsers = PamToken::where('account_id', $event->pam->id)
            ->where('device_type', '!=', $event->deviceType)
            ->get();

        if ($logoutUsers->count()) {
            PamToken::where('account_id', $event->pam->id)->where('device_type', '!=', $event->deviceType)->delete();
        }
        event(new PamSsoEvent($event->pam, $logoutUsers));
    }
}
