<?php namespace Poppy\System\Http\Middlewares;

use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

/**
 * csrf 校验, 暂时均未开启
 */
class VerifyCsrfToken extends BaseVerifier
{
	/**
	 * The URIs that should be excluded from CSRF verification.
	 * @var array
	 */
	protected $except = [];

	/**
	 * Create a new middleware instance.
	 *
	 * @param Application $app
	 * @param Encrypter   $encrypter
	 * @return void
	 */
	public function __construct(Application $app, Encrypter $encrypter)
	{
		parent::__construct($app, $encrypter);
		$this->except = (array) config('module.system.csrf_except');
	}

	/**
	 * Handle an incoming request.
	 * @param Request $request 请求
	 * @param Closure $next    后续处理
	 * @return mixed
	 * @throws TokenMismatchException
	 */
	public function handle($request, Closure $next)
	{
		return parent::handle($request, $next);
	}
}