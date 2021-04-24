<?php

namespace Poppy\System\Http\Request\ApiV1\Web;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\Misc\MiscArea;

/**
 * 杂项
 */
class MiscController extends WebApiController
{
    use PoppyTrait, ThrottlesLogins;

    /**
     * @api              {post} api_v1/system/misc/area [Sys]地区
     * @apiVersion       1.0.0
     * @apiName          SysMiscArea
     * @apiGroup         Poppy
     */
    public function area()
    {
        $area = MiscArea::area();
        $tree = UtilHelper::genTree($area, 'id', 'parent_id');
        return Resp::success(
            '获取地区成功',
            $tree
        );
    }

    /**
     * @api              {post} api_v1/system/misc/country [Sys]国别
     * @apiVersion       1.0.0
     * @apiName          SysMiscCountry
     * @apiGroup         Poppy
     */
    public function country()
    {
        return Resp::success(
            '获取成功',
            MiscArea::country()
        );
    }
}