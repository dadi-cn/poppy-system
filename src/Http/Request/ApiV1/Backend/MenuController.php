<?php

namespace Poppy\System\Http\Request\ApiV1\Backend;

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrPage\Http\Request\Backend\BackendController;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

/**
 * 系统信息控制
 */
class MenuController extends BackendController
{
    use CoreTrait;

    /**
     * @api                    {post} api_v1/backend/system/menu/lists [Sys]菜单
     * @apiVersion             1.0.0
     * @apiName                SystemMenuList
     * @apiGroup               Backend
     */
    public function lists()
    {
        $isFullPermission = $this->pam->hasRole(PamRole::BE_ROOT);
        $data             = $this->coreModule()->menus()->withPermission(PamAccount::TYPE_BACKEND, $isFullPermission, $this->pam);
        return Resp::success('菜单信息', $data);
    }
}