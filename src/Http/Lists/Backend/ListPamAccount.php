<?php

namespace Poppy\System\Http\Lists\Backend;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Action\Pam;
use Poppy\System\Classes\Grid\Column;
use Poppy\System\Classes\Grid\Displayer\Actions;
use Poppy\System\Classes\Grid\Filter;
use Poppy\System\Classes\Grid\Tools\BaseButton;
use Poppy\System\Http\Lists\ListBase;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\PamRoleAccount;

class ListPamAccount extends ListBase
{

    public $title = '账号管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('username', "用户名");
        $this->column('mobile', "手机号");
        $this->column('email', "邮箱");
        $this->column('login_times', "登录次数");
        $this->column('created_at', "操作时间");
        $this->column('type', "账号类型");
    }

    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $type  = input('_scope', PamAccount::TYPE_BACKEND);
            $roles = PamRole::getLinear($type);
            $filter->column(1 / 12, function (Filter $ft) {
                $ft->where(function ($query) {
                    $passport = input('passport');
                    $type     = (new Pam())->passportType($passport);
                    $query->where($type, $passport);
                }, '手机/用户名/邮箱', 'passport');
            });
            $filter->column(1 / 12, function (Filter $column) use ($roles) {
                $column->where(function ($query) {
                    $roleId      = data_get($this, 'input');
                    $account_ids = PamRoleAccount::where('role_id', $roleId)->pluck('account_id');
                    $query->whereIn('id', $account_ids);
                }, '用户角色', 'role_id')->select($roles);
            });
            $types = PamAccount::kvType();
            foreach ($types as $t => $v) {
                $filter->scope($t, $v)->where('type', $t);
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $Action = $this;
        $this->addColumn(Column::NAME_ACTION, '操作')
            ->displayUsing(Actions::class, [
                function (Actions $actions) use ($Action) {
                    $item = $actions->row;
                    $actions->append([
                        $Action->password($item),
                        $Action->disable($item),
                        $Action->enable($item),
                        $Action->edit($item),
                    ]);
                },
            ]);
    }


    public function quickButtons(): array
    {
        return [
            $this->create(input(Filter\Scope::QUERY_NAME)),
        ];
    }


    /**
     * 创建
     * @param $type
     * @return BaseButton
     */
    public function create($type): BaseButton
    {
        return new BaseButton('<i class="fa fa-plus"></i> 新增', route_url('py-mgr-page:backend.pam.establish', null, ['type' => $type]), [
            'title' => "修改密码",
            'class' => 'J_iframe layui-btn layui-btn-sm',
        ]);
    }

    /**
     * 修改密码
     * @param $item
     * @return BaseButton
     */
    public function password($item): BaseButton
    {
        return new BaseButton('<i class="fa fa-key"></i>', route('py-mgr-page:backend.pam.password', [$item->id]), [
            'title' => "修改密码",
            'class' => 'J_iframe',
        ]);
    }


    /**
     * 编辑
     * @param $item
     * @return BaseButton
     */
    public function edit($item): BaseButton
    {
        return new BaseButton('<i class="fa fa-edit"></i>', route('py-mgr-page:backend.pam.establish', [$item->id]), [
            'title' => "编辑[{$item->username}]",
            'class' => 'J_iframe',
        ]);
    }

    /**
     * 禁用
     * @param $item
     * @return BaseButton|null
     */
    public function disable($item): ?BaseButton
    {
        if ($this->pam->can('disable', $item)) {
            return new BaseButton('<i class="fa fa-unlock text-success"></i>', route_url('py-mgr-page:backend.pam.disable', [$item->id]), [
                'title' => '当前启用, 点击禁用',
                'class' => 'J_iframe',
            ]);
        }
        return null;
    }

    /**
     * 启用
     * @param $item
     * @return BaseButton|null
     */
    public function enable($item): ?BaseButton
    {
        if ($this->pam->can('enable', $item)) {
            return new BaseButton('<i class="fa fa-lock"></i>', route_url('py-mgr-page:backend.pam.enable', [$item->id]), [
                'title' => '当前禁用, 点击启用',
                'class' => 'J_iframe',
            ]);
        }
        return null;
    }

}
