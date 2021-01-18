<?php namespace Poppy\System\Models\Actions;

use Poppy\System\Classes\Grid\Tools\BaseButton;
use Poppy\System\Models\PamAccount;

/**
 * 用户filter
 */
class PamAccountAction extends BaseAction
{

    /**
     * @var PamAccount
     */
    protected $item;


    /**
     * 创建
     * @param $type
     * @return BaseButton
     */
    public function create($type): BaseButton
    {
        $url = route_url('py-mgr-page:backend.pam.establish', null, ['type' => $type]);
        return new BaseButton('create', '新增', $url, '<i class="fa fa-plus"></i> 新增', 'J_iframe layui-btn layui-btn-sm layui-btn-normal');
    }

    /**
     * 修改密码
     * @return BaseButton
     */
    public function password(): BaseButton
    {
        $url = route('py-mgr-page:backend.pam.password', [$this->item->id]);
        return new BaseButton('password', '修改密码', $url, '<i class="fa fa-key"></i>', 'J_iframe J_tooltip');
    }


    public function edit(): BaseButton
    {
        $url = route('py-mgr-page:backend.pam.establish', [$this->item->id]);
        return new BaseButton('edit', "编辑[{$this->item->username}]", $url, '<i class="fa fa-edit"></i>', 'J_iframe J_tooltip');
    }


    public function disable(): ?BaseButton
    {
        if ($this->pam->can('disable', $this->item)) {
            $url = route_url('py-mgr-page:backend.pam.disable', [$this->item->id]);
            return new BaseButton('disable', '当前启用, 点击禁用', $url, '<i class="fa fa-unlock text-success"></i>', 'J_iframe J_tooltip');

        }
        return null;
    }

    public function enable(): ?BaseButton
    {
        if ($this->pam->can('enable', $this->item)) {
            $url = route_url('py-mgr-page:backend.pam.enable', [$this->item->id]);
            return new BaseButton('enable', '当前禁用, 点击启用', $url, '<i class="fa fa-lock"></i>', 'J_iframe J_tooltip');
        }
        return null;
    }

}