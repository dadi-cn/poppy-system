<?php

namespace Poppy\System\Events;

use Poppy\System\Models\PamAccount;

/**
 * 用户颁发token 成功
 */
class LoginTokenPassedEvent
{
    /**
     * @var PamAccount 用户账户
     */
    public $pam;

    /**
     * @var string
     */
    public $token;

    public function __construct(PamAccount $pam, string $token)
    {
        $this->pam   = $pam;
        $this->token = $token;
    }
}