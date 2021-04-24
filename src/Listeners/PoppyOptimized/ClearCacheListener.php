<?php

namespace Poppy\System\Listeners\PoppyOptimized;

use Poppy\Framework\Events\PoppyOptimized;
use Poppy\System\Classes\PySystemDef;

/**
 * 清除缓存
 */
class ClearCacheListener
{

    /**
     * @param PoppyOptimized $event 框架优化
     */
    public function handle(PoppyOptimized $event)
    {
        sys_cache('py-system')->forget(PySystemDef::ckCountry());
        sys_cache('py-system')->forget(PySystemDef::ckArea());
    }
}

