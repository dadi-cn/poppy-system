<?php

namespace Poppy\System\Events;

/**
 * 发送验证码前的校验, 用于验证国际手机号
 */
class CaptchaBeforeSendEvent
{

    /**
     * 通行证
     * @var string
     */
    public $passport;

    public function __construct($passport)
    {
        $this->passport = $passport;
    }
}