<?php

namespace Poppy\System\Http\Forms\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Poppy\Core\Classes\Contracts\SettingContract;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\System\Classes\Widgets\FormWidget;
use Poppy\System\Exceptions\FormException;
use Response;

abstract class FormSettingBase extends FormWidget
{
    use KeyParserTrait;

    public $ajax = true;
    public $inbox = false;
    protected $title = '';
    protected $withContent = false;
    protected $group = '';

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\Response|JsonResponse|Redirector|RedirectResponse|Resp|Response
     * @throws FormException
     */
    public function handle(Request $request)
    {
        $Setting = app(SettingContract::class);
        $all     = $request->all();
        foreach ($all as $key => $value) {
            if (is_null($value)) {
                $value = '';
            }
            $fullKey = $this->group . '.' . $key;
            $class   = __CLASS__;
            if (!$this->keyParserMatch($fullKey)) {
                throw new FormException("Key {$fullKey} Not Match At Group `{$this->group}` In Class `{$class}`");
            }
            $Setting->set($fullKey, $value);
        }

        return Resp::success('更新配置成功', '_reload|1');
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $Setting = app(SettingContract::class);
        $data    = [];
        foreach ($this->fields() as $field) {
            if (Str::startsWith($field->column(), '_')) {
                continue;
            }
            $data[$field->column()] = $Setting->get($this->group . '.' . $field->column());
        }
        return $data;
    }
}
