<?php

use Illuminate\Routing\Router;

Route::group([
	'namespace' => 'Poppy\System\Http\Request\Web',
], function (Router $router) {
	$router->any('captcha/{type}/{width?}/{height?}/{length?}', 'CaptchaController@image')
		->name('system:web.captcha.session');
	$router->any('res/mix/{key?}', 'ResController@mix')
		->name('system:web.res.mix');
	$router->any('res/translate', 'ResController@translate')
		->name('system:web.res.translate');
	$router->any('ph/img/{spec}/{text?}', 'PlaceholderController@image')
		->name('system:web.ph.image');
});
