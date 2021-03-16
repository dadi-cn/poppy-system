<?php

namespace Poppy\System\Http\Request\ApiV1\Web;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Poppy\Framework\Classes\Resp;

/**
 * 系统信息控制
 */
class CoreController extends WebApiController
{
    use ThrottlesLogins;

    /**
     * @api                    {post} api_v1/system/core/info 系统信息
     * @apiVersion             1.0.0
     * @apiName                SystemInfo
     * @apiGroup               System
     */
    public function info()
    {

        $hook   = sys_hook('poppy.system.api_info');
        $system = array_merge([], $hook);

        return Resp::web(Resp::SUCCESS, '获取系统配置信息', $system);
    }


    /**
     * @api                    {post} api_v1/system/core/translate 多语言包
     * @apiVersion             1.0.0
     * @apiName                SystemTranslate
     * @apiGroup               System
     */
    public function translate()
    {
        return Resp::success('翻译信息', [
            'json'         => true,
            'translations' => app('translator')->fetch('zh'),
        ]);
    }
}