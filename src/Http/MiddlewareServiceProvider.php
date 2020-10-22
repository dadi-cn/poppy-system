<?php namespace Poppy\System\Http;

use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Http\Middlewares\CrossPreflight;
use Poppy\Framework\Http\Middlewares\EnableCrossRequest;
use Poppy\System\Http\Middlewares\RbacPermission;

class MiddlewareServiceProvider extends ServiceProvider
{
	public function boot(Router $router)
	{
		/* Single
		 * ---------------------------------------- */
		$router->aliasMiddleware('permission', RbacPermission::class);
		$router->aliasMiddleware('auth', Middlewares\Authenticate::class);
		$router->aliasMiddleware('auth_session', Middlewares\AuthenticateSession::class);
		$router->aliasMiddleware('disabled_pam', Middlewares\DisabledPam::class);
		$router->aliasMiddleware('app_sign', Middlewares\AppSign::class);
		$router->aliasMiddleware('be_append_data', Middlewares\Backend\AppendData::class);
		$router->aliasMiddleware('site_open', Middlewares\SiteOpen::class);
		$router->aliasMiddleware('csrf_token', Middlewares\VerifyCsrfToken::class);

		/* Web
		 * ---------------------------------------- */
		$router->pushMiddlewareToGroup('web', Middlewares\EncryptCookies::class);
		$router->pushMiddlewareToGroup('web', Middlewares\VerifyCsrfToken::class);

		/*
		|--------------------------------------------------------------------------
		| Web Auth
		|--------------------------------------------------------------------------
		|
		*/
		$router->middlewareGroup('web-auth', [
			'web',
			'site_open',
			'auth:web',
			'auth_session',
			'disabled_pam',
		]);

		$router->middlewareGroup('mobile-auth', [
			'web',
			'site_open',
			'auth:web,jwt',
			'auth_session',
			'disabled_pam',
		]);

		$router->middlewareGroup('develop-auth', [
			'web',
			'site_open',
			'auth:develop',
			'auth_session',
			'disabled_pam',
		]);

		$router->middlewareGroup('backend-auth', [
			'web',
			'auth:backend',
			'auth_session',
			'disabled_pam',
			'be_append_data',
			'permission',
		]);

		/*
		|--------------------------------------------------------------------------
		| Api Middleware
		|--------------------------------------------------------------------------
		*/

		$router->middlewareGroup('api-sign', [
			'app_sign',
			'site_open',
		]);

		$router->middlewareGroup('api-auth', [
			'app_sign',
			'site_open',
			'auth:jwt_web',
			'disabled_pam',
		]);

		// add options
		if ($this->app->make('request')->getMethod() === 'OPTIONS') {
			$this->app->make(KernelContract::class)->prependMiddleware(CrossPreflight::class);
		}
		// cors for api
		$this->app->make(KernelContract::class)->prependMiddleware(EnableCrossRequest::class);
	}
}