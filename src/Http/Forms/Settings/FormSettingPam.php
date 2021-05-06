<?php

namespace Poppy\System\Http\Forms\Settings;

use Poppy\Framework\Validation\Rule;

class FormSettingPam extends FormSettingBase
{
    protected $title = 'Pam设置';

    protected $group = 'py-system::pam';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('disabled_reason', '账号被封禁原因')->rules([
            Rule::nullable(),
        ])->placeholder('账号被封禁原因');
        $this->text('prefix', '账号前缀')->rules([
            Rule::required(),
        ])->placeholder('请输入账号前缀, 用于账号注册默认用户名生成');
        $this->textarea('test_account', '测试账号')->placeholder('请填写测试账号, 每行一个')->help('在此测试账号内的应用, 不需要正确的验证码即可登录');
    }
}
