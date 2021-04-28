<?php

namespace Poppy\System\Listeners\LoginTokenPassed;

use Carbon\Carbon;
use Poppy\AliyunPush\Classes\AliPush;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\System\Events\LoginTokenPassedEvent;
use Request;
use User\Models\PamToken;

/**
 * 记录设备信息
 */
class CreateUserDeviceListener
{
    /**
     * Handle the event.
     * @param LoginTokenPassedEvent $event 用户账号
     * @return void
     */
    public function handle(LoginTokenPassedEvent $event)
    {
        // 关闭设备登录
        if (!sys_setting('misc::site.device_type')) {
            return;
        }

        $tokenMd5 = md5($event->token);
        $pamId    = $event->pam->id;


        PamToken::updateOrInsert([
            'account_id'  => $pamId,
            'device_type' => $pamId,
        ]);

        $aliPushIds = PamToken::where('token_hash', '!=', $tokenMd5)
            ->where('device_type', misc_header_info('os') ?: 'web')
            ->where('account_id', $event->pam->id)
            ->pluck('ali_push_id')
            ->toArray();
        if ($aliPushIds) {
            // 推送下线
            $param = [
                // 本地使用 tag, 则进行测试
                'broadcast_type'   => 'DEVICE',
                'registration_ids' => $aliPushIds,
                'title'            => '异地登录提醒',
                'content'          => '尊敬的用户，您的账号在异地登录，如不是本人操作请及时修改密码。',
                'extras'           => [

                ],
                'offline'          => 'Y',
                'device_type'      => 'android|message',
            ];

            $Push = new AliPush();
            if (!$Push->send($param)) {

            }
        }


        // 记录设备信息
        PamToken::create([
            'account_id'  => $event->pam->id,
            'device_id'   => EnvHelper::agent(),
            'device_type' => Request::header('X-APP-OS') ?: 'web',
            'login_ip'    => EnvHelper::ip(),
            'token_hash'  => md5($event->token),
            'expired_at'  => Carbon::now()->addWeek()->toDateTimeString(),
        ]);
    }
}
