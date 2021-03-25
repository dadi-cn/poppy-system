<?php

namespace Poppy\System\Http;

use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Poppy\System\Http\Middlewares\CrossRequest;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        /* Single
         * ---------------------------------------- */
        $router->aliasMiddleware('sys-auth', Middlewares\Authenticate::class);
        $router->aliasMiddleware('sys-auth_session', Middlewares\AuthenticateSession::class);
        $router->aliasMiddleware('sys-disabled_pam', Middlewares\DisabledPam::class);
        $router->aliasMiddleware('sys-site_open', Middlewares\SiteOpen::class);
        $router->aliasMiddleware('sys-app_sign', Middlewares\AppSign::class);
        $router->aliasMiddleware('sys-csrf_token', Middlewares\VerifyCsrfToken::class);
        $router->aliasMiddleware('sys-encrypt_cookies', Middlewares\EncryptCookies::class);

        /*
        |--------------------------------------------------------------------------
        | Web Middleware
        |--------------------------------------------------------------------------
        |
        */
        $router->middlewareGroup('web-dft', [
            'web',
            'sys-site_open',
            'sys-csrf_token',
            'sys-encrypt_cookies',
        ]);

        $router->middlewareGroup('web-auth', [
            'web-dft',
            'sys-auth:web',
            'sys-auth_session',
            'sys-disabled_pam',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Api Middleware
        |--------------------------------------------------------------------------
        */

        $router->middlewareGroup('api-sign', [
            'sys-app_sign',
            'sys-site_open',
        ]);


        // cors for api
        $this->app->make(KernelContract::class)->prependMiddleware(CrossRequest::class);
    }
}