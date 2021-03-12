<?php

namespace Poppy\System\Http\Lists\Backend;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Classes\Grid\Filter;
use Poppy\System\Http\Lists\ListBase;

/**
 * 列表 PamLog
 */
class ListPamLog extends ListBase
{

    /**
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('pam.username', "用户名");
        $this->column('created_at', "操作时间");
        $this->column('ip', "IP地址");
        $this->column('type', "状态");
        $this->column('area_text', "说明");
    }


    public function seek(): Closure
    {
        return function (Filter $filter) {
            $filter->column(1 / 12, function (Filter $column) {
                $column->equal('account_id', '用户ID');
            });
            $filter->column(1 / 12, function (Filter $column) {
                $column->equal('ip', 'IP地址');
            });
            $filter->column(1 / 12, function (Filter $column) {
                $column->like('area_text', '登录地区');
            });
        };
    }
}
