<?php namespace Poppy\System\Http\Lists\Backend;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Classes\Grid\Column;
use Poppy\System\Classes\Grid\Displayers\Actions;
use Poppy\System\Classes\Grid\Filter;
use Poppy\System\Classes\Grid\Tools\BaseButton;
use Poppy\System\Http\Lists\ListBase;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\PamRoleAccount;

class ListPamAccount extends ListBase
{

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
    public function seek(): Closure
    {
        return function (Filter $filter) {
            $type  = input('_scope_', PamAccount::TYPE_BACKEND);
            $roles = PamRole::getLinear($type);
            $filter->column(1 / 12, function (Filter $column) {
                $column->equal('passport', '手机/用户名/邮箱');
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
        $this->addColumn(Column::ACTION_COLUMN_NAME, '操作')
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
        $url = route_url('py-mgr-page:backend.pam.establish', null, ['type' => $type]);
        return new BaseButton('create', '新增', $url, '<i class="fa fa-plus"></i> 新增', 'J_iframe layui-btn layui-btn-sm layui-btn-normal');
    }

    /**
     * 修改密码
     * @param $item
     * @return BaseButton
     */
    public function password($item): BaseButton
    {
        $url = route('py-mgr-page:backend.pam.password', [$item->id]);
        return new BaseButton('password', '修改密码', $url, '<i class="fa fa-key"></i>', 'J_iframe J_tooltip');
    }


    /**
     * 编辑
     * @param $item
     * @return BaseButton
     */
    public function edit($item): BaseButton
    {
        $url = route('py-mgr-page:backend.pam.establish', [$item->id]);
        return new BaseButton('edit', "编辑[{$item->username}]", $url, '<i class="fa fa-edit"></i>', 'J_iframe J_tooltip');
    }

    /**
     * 禁用
     * @param $item
     * @return BaseButton|null
     */
    public function disable($item): ?BaseButton
    {
        if ($this->pam->can('disable', $item)) {
            $url = route_url('py-mgr-page:backend.pam.disable', [$item->id]);
            return new BaseButton('disable', '当前启用, 点击禁用', $url, '<i class="fa fa-unlock text-success"></i>', 'J_iframe J_tooltip');

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
            $url = route_url('py-mgr-page:backend.pam.enable', [$item->id]);
            return new BaseButton('enable', '当前禁用, 点击启用', $url, '<i class="fa fa-lock"></i>', 'J_iframe J_tooltip');
        }
        return null;
    }

}
