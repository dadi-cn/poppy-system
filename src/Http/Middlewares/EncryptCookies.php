<?php namespace Poppy\System\Http\Middlewares;

use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;

/**
 * csrf 校验, 暂时均未开启
 */
class EncryptCookies extends \Illuminate\Cookie\Middleware\EncryptCookies
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * @var array
     */
    protected $except;

    public function __construct(EncrypterContract $encrypter)
    {
        parent::__construct($encrypter);
        $this->append();
    }

    /**
     * Handle an incoming request.
     * @return mixed
     */
    private function append()
    {
        $this->except = (array) config('poppy.system.uncrypt_cookies');
    }
}