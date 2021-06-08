<?php

namespace Poppy\System\Classes\Api\Sign;

use Poppy\Framework\Helper\ArrayHelper;

/**
 * 后台用户认证
 */
class DefaultApiSignProvider extends DefaultBaseApiSign
{
    /**
     * 获取Sign
     * @param array $params 请求参数
     * @return string
     */
    public function sign(array $params): string
    {
        $dirtyParams = $params;
        $params      = $this->except($params);
        $token       = jwt_token($params);
        ksort($params);
        $kvStr    = ArrayHelper::toKvStr($params);
        $signLong = md5(md5($kvStr) . $token($dirtyParams));
        return $signLong[1] . $signLong[3] . $signLong[15] . $signLong[31];
    }
}