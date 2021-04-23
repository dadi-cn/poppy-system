<?php
/*
|--------------------------------------------------------------------------
| 系统路由
|--------------------------------------------------------------------------
|
*/
Route::group([
    'middleware' => ['api-sign'],
    'prefix'     => 'system',
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('auth/login', 'AuthController@login')
        ->name('py-system:pam.auth.login');
    $route->post('auth/access', 'AuthController@access')
        ->name('py-system:pam.auth.access');

    // captcha
    $route->post('captcha/verify_code', 'CaptchaController@verifyCode');
    $route->post('captcha/send', 'CaptchaController@send');
    $route->post('captcha/fetch', 'CaptchaController@fetch');

    // info
    $route->post('core/info', 'CoreController@info');
    $route->post('core/translate', 'CoreController@translate');
    $route->post('core/mock', 'CoreController@mock');

    // auth
    $route->post('auth/reset_password', 'AuthController@resetPassword');
    $route->post('auth/bind_mobile', 'AuthController@bindMobile');
    $route->group([
        'middleware' => ['sys-auth:jwt', 'sys-disabled_pam'],
    ], function (Illuminate\Routing\Router $route) {
        $route->post('upload/image', 'UploadController@image')
            ->name('py-system:api_v1.upload.image');
        $route->post('upload/file', 'UploadController@file')
            ->name('py-system:api_v1.upload.file');
    });
});

