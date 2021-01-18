<?php namespace Poppy\System\Http\Forms\Backend;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;

class FormPamDisable extends FormDialogWidget
{
    public $ajax = true;

    private $id;

    /**
     * @var PamAccount
     */
    private $pam;

    /**
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id) {
            $this->pam = PamAccount::passport($this->id);

            if (!$this->pam) {
                throw  new ApplicationException('无用户数据');
            }

        }
        return $this;
    }

    public function handle()
    {
        $id = input('id');
        if (!$id) {
            return Resp::error('您尚未选择用户!');
        }

        $this->setId($id);
        $date   = input('date', '');
        $reason = input('reason', '');
        $Pam    = (new Pam())->setPam($this->pam);
        if (!$Pam->disable($id, $date, $reason)) {
            return Resp::error($Pam->getError());
        }

        return Resp::success('当前用户已封禁', '_top_reload|1');

    }

    public function data()
    {
        if ($this->id) {
            return [
                'id' => $this->pam->id,
            ];
        }
        return [];
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
        }
        $this->date('date', '解禁日期')->rules([
            Rule::required(),
            Rule::date(),
        ]);
        $this->textarea('reason', '封禁原因');
    }
}
