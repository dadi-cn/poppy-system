<?php

namespace Poppy\System\Http\Middlewares;

use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;

/**
 * 加密Cookie
 */
class EncryptCookies extends \Illuminate\Cookie\Middleware\EncryptCookies
{

    public function __construct(EncrypterContract $encrypter)
    {
        parent::__construct($encrypter);
        $this->except = (array) config('poppy.system.uncrypt_cookies');
    }
}