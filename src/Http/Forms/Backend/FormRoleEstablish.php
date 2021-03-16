<?php

namespace Poppy\System\Http\Forms\Backend;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Role;
use Poppy\System\Classes\Widgets\FormWidget;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

class FormRoleEstablish extends FormWidget
{

    public $ajax = true;

    protected $width = [
        'label' => 3,
        'field' => 9,
    ];

    private $id;

    /**
     * @var PamRole
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id) {
            $this->item = PamRole::find($id);

            if (!$this->item) {
                throw  new ApplicationException('无用户数据');
            }
        }
        return $this;
    }

    public function handle()
    {
        $id   = input('id');
        $Role = (new Role())->setPam(request()->user());
        if (is_post()) {
            $this->setId($id);
            if ($Role->establish(request()->all(), $id)) {
                return Resp::success('操作成功', '_top_reload|1');
            }

            return Resp::error($Role->getError());
        }
        $id && $Role->init($id) && $Role->share();
    }

    public function data()
    {
        if ($this->id) {
            return [
                'id'            => $this->item->id,
                'title'         => $this->item->title,
                'name'          => $this->item->name,
                'guard'         => $this->item->type,
                'guard-display' => $this->item->type,
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
            $this->hidden('guard', 'Guard');
            $this->select('guard-display', '角色组')->options(PamAccount::kvType())->rules([
                Rule::required(),
            ])->disable()->attribute([
                'lay-ignore',
            ]);
            $this->text('name', '角色标识')->disable()->readonly();
        }
        else {
            $this->select('guard', '角色组')->options(PamAccount::kvType())->rules([
                Rule::required(),
            ]);
            $this->text('name', '角色标识');
        }

        $this->text('title', '角色名称')->rules([
            Rule::required(),
        ]);
    }
}
