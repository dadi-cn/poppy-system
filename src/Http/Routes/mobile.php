<?php

use Illuminate\Routing\Router;

Route::group([
	'namespace' => 'Poppy\System\Http\Request\Mobile',
], function (Router $router) {
	$router->post('captcha/send', 'CaptchaController@send')
		->name('system:mobile.captcha.send');
});