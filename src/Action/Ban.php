<?php

namespace Poppy\System\Action;

use Carbon\Carbon;
use Exception;
use Poppy\Core\Redis\RdsDb;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\PySystemDef;
use Poppy\System\Events\PamTokenBanEvent;
use Poppy\System\Models\PamBan;
use Poppy\System\Models\PamToken;
use Throwable;

/**
 * 用户禁用
 */
class Ban
{
    use AppTrait;

    static $rds;

    static $banKey;

    public function __construct()
    {
        self::$rds    = RdsDb::instance();
        self::$banKey = 'py-system:' . PySystemDef::ckBan();
    }


    /**
     *  封禁
     * @param array $input
     * @return bool
     */
    public function establish(array $input): bool
    {
        $type  = data_get($input, 'type', '');
        $value = trim(data_get($input, 'value', ''));

        if (!array_key_exists($type, PamBan::kvType())) {
            return $this->setError('请选择正确的类型');
        }

        // ip 合法性
        if ($type === PamBan::TYPE_IP && !UtilHelper::isIp($value)) {
            return $this->setError('IP地址不合法');
        }

        if (PamBan::where('type', $type)->where('value', $value)->exists()) {
            return $this->setError('条目已存在!');
        }

        PamBan::create([
            'type'  => $type,
            'value' => $value,
        ]);

        $this->init();
        $key = md5($value);
        self::$rds->hSet(self::$banKey, $key, $value . '|ban|' . Carbon::now()->toDateTimeString());
        return true;
    }

    /**
     * 删除
     * @param int $id id
     * @return bool
     */
    public function delete(int $id): bool
    {
        if (!$ban = PamBan::find($id)) {
            return $this->setError('条目不存在');
        }

        $this->init();
        try {
            // 删除与类型相关的Hash
            $key = md5($ban->value);
            self::$rds->hSet(self::$banKey, $key, $ban->value . '|ban|' . Carbon::now()->toDateTimeString());

            $ban->delete();
            return true;
        } catch (Exception $e) {
            return $this->setError('删除失败');
        }
    }

    /**
     * 禁用 Ban
     * @param $id
     * @param $type
     * @return bool
     */
    public function type($id, $type): bool
    {
        /** @var PamToken $item */
        $item = PamToken::find($id);
        if (!in_array($type, array_keys(PamBan::kvType()))) {
            return $this->setError('封禁类型错误');
        }

        $this->init();

        if ($type === PamBan::TYPE_IP) {
            $ip = $item->login_ip;
            if (!$ip || UtilHelper::isLocalIp($ip)) {
                return $this->setError('用户IP不存在或者是局域网IP, 不可封禁');
            }
            if (PamBan::where('type', PamBan::TYPE_IP)->where('value', $ip)->exists()) {
                return $this->setError('该IP已经被封禁');
            }
            $key   = md5($item->login_ip);
            $value = Carbon::now()->toDateTimeString();
            PamBan::create([
                'type'  => PamBan::TYPE_IP,
                'value' => $ip,
            ]);
            self::$rds->hSet(self::$banKey, $key, $item->login_ip . '|user|' . $value);
        }
        if ($type === PamBan::TYPE_DEVICE) {
            $deviceId = $item->device_id;
            if (!$deviceId) {
                return $this->setError('用户设备ID号为空, 不得操作');
            }
            $key   = md5($item->device_id);
            $value = Carbon::now()->toDateTimeString();
            self::$rds->hSet(self::$banKey, $key, $item->device_id . '|' . $item->device_type . '|' . $value);
        }
        try {
            $item->delete();
            event(new PamTokenBanEvent($item, $type));
        } catch (Throwable $e) {
            return $this->setError($e->getMessage());
        }
        return true;
    }

    /**
     * 记录可用Token/记录过期时间
     * @param int    $account_id
     * @param string $md5Token
     * @param Carbon $expired_at
     */
    public function token(int $account_id, string $md5Token, Carbon $expired_at)
    {
        // 记录可用Token/记录过期时间
        $Rds = RdsDb::instance();
        $Rds->hSet('py-system:' . PySystemDef::ckSso('valid'), $account_id, $md5Token . '|' . $expired_at->toDateTimeString());
        $Rds->zAdd('py-system:' . PySystemDef::ckSso('expired'), [
            $account_id => $expired_at->timestamp,
        ]);
    }

    /**
     * 初始化
     */
    public function init()
    {
        if (self::$rds->exists(self::$banKey)) {
            return;
        }
        $items = PamBan::get();
        // 保障KEY存在
        self::$rds->hSet(self::$banKey, str_repeat('111111', 4), md5('duoli') . '|init|' . Carbon::now()->toDateTimeString());
        $values = collect();
        $now    = Carbon::now()->toDateTimeString();
        collect($items)->each(function ($item) use ($values, $now) {
            $values->offsetSet(md5($item->value), $item->value . '|init|' . $now);
        });
        self::$rds->hMSet(self::$banKey, $values->toArray());
    }
}
