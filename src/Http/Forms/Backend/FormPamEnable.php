<?php namespace Poppy\System\Http\Forms\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use Response;

class FormPamEnable extends FormDialogWidget
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
            $this->pam = PamAccount::find($this->id);

            if (!$this->pam) {
                throw  new ApplicationException('无用户数据');
            }

        }
        return $this;
    }

    /**
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|mixed|Resp|Response
     * @throws ApplicationException
     */
    public function handle()
    {
        $id = input('id');
        if (!$id) {
            return Resp::error('您尚未选择用户!');
        }

        $this->setId($id);
        $Pam      = (new Pam())->setPam($this->pam);
        $reason   = input('reason', '');
        $pictures = input('pictures', []);
        if (!$Pam->enable($id, $reason, $pictures)) {
            return Resp::error($Pam->getError());
        }

        return Resp::success('当前用户启用', '_top_reload|1');

    }

    public function data()
    {
        if ($this->id) {
            return [
                'id'   => $this->pam->id,
                'date' => $this->pam->disable_end_at,
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
        $this->date('date', '解禁日期')->disable();
        $this->textarea('reason', '原因');
    }
}
