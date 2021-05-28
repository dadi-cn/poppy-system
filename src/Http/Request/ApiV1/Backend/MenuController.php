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
        $data             = collect($data)->map(function ($module) {
            $module['key'] = $module['parent'];
            unset($module['type'], $module['routes'], $module['enabled'], $module['parent']);
            $groups           = collect($module['groups'])->map(function ($group) {
                $children = $group['children'] ?? [];
                if (count($children)) {
                    $children          = collect($children)->map(function ($child) {
                        $fnChild           = function ($child) {
                            if (($child['route'] ?? '') || ($child['url'] ?? '')) {
                                $child['url'] = $child['url'] ?? route_url($child['route'], $child['route_param'] ?? [], $child['param'] ?? []);
                            }
                            unset($child['key'], $child['route_param'], $child['param'], $child['routes']);
                            return $child;
                        };
                        $subChildren       = $child['children']??[];
                        if ($subChildren) {
                            $child['children'] = collect($subChildren)->map(function ($child) use ($fnChild) {
                                return $fnChild($child);
                            });
                        }
                        return $fnChild($child);
                    })->toArray();
                    $group['children'] = $children;
                }
                unset($group['key'], $group['route_param'], $group['param'], $group['routes']);
                return $group;
            });
            $module['groups'] = $groups;
            return $module;
        })->toArray();
        return Resp::success('菜单信息', $data);
    }
}