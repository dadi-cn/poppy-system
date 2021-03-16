<?php namespace Poppy\System\Http\Forms\Backend;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

class FormPamEstablish extends FormDialogWidget
{

    public $ajax = true;

    private $type;

    private $id;

    /**
     * @var PamAccount
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id): self
    {
        $this->id = $id;

        if ($id) {
            $this->item = PamAccount::passport($this->id);

            if (!$this->item) {
                throw  new ApplicationException('无用户数据');
            }
            $this->type = $this->item->type;
        }
        return $this;
    }

    /**
     * 设置类型
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function handle()
    {
        $username = input('username');
        $password = input('password');
        $role_id  = input('role_id');
        $id       = input('id');

        if (!$role_id) {
            return Resp::error('请选择角色');
        }
        if ($id) {
            $this->setId($id);
            $Pam = new Pam();
            if ($password) {
                $Pam->setPassword($this->item, $password);
            }
            $Pam->setRoles($this->item, $role_id);
            return Resp::success('用户修改成功', '_top_reload|1');
        }

        $Pam = new Pam();
        if ($Pam->register($username, $password, $role_id)) {
            return Resp::success('用户添加成功', '_top_reload|1');
        }
        return Resp::error($Pam->getError());
    }

    public function data(): array
    {
        if ($this->id) {
            return [
                'id'       => $this->item->id,
                'username' => $this->item->username,
                'role_id'  => $this->item->roles->pluck('id')->toArray(),
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
            $this->text('username', '用户名')->readonly()->disable();
            $this->tags('role_id', '用户角色')->options(PamRole::getLinear($this->type, 'id'))->readonly();

        }
        else {
            $this->text('username', '用户名')->rules([
                Rule::nullable(),
            ]);
            $this->tags('role_id', '用户角色')->options(PamRole::getLinear($this->type, 'id'));
        }

        $this->password('password', '密码');
        $this->hidden('type', $this->type)->default($this->type);
    }
}
