<?php
/*
|--------------------------------------------------------------------------
| 系统路由
|--------------------------------------------------------------------------
|
*/
Route::group([
	'prefix'     => 'system',
	'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
	// 登录暂时不进行验签
	$route->post('auth/access', 'AuthController@access')->name('system:pam.auth.access');
	$route->post('auth/token/{guard}', 'AuthController@token')->name('system:pam.auth.token');

	$route->group([
		'middleware' => ['app_sign'],
	], function (Illuminate\Routing\Router $route) {
		$route->get('captcha/image', 'CaptchaController@image');    // 获取图像验证
		$route->post('captcha/send', 'CaptchaController@send')
			->name('system:api_v1.captcha.send');                     // 发送验证码
		$route->post('captcha/verify_code', 'CaptchaController@verifyCode');

		// info
		$route->post('core/info', 'CoreController@info');

		// auth
		$route->post('auth/reset_password', 'AuthController@resetPassword');
		$route->post('auth/login', 'AuthController@login');
	});

	$route->group([
		'middleware' => ['auth:jwt', 'disabled_pam'],
	], function (Illuminate\Routing\Router $route) {
		$route->post('image/upload', 'UploadController@image');
		$route->post('upload/image', 'UploadController@image')
			->name('system:api_v1.upload.image');
		$route->post('upload/file', 'UploadController@file')
			->name('system:api_v1.upload.file');
	});
});

