<?php

use Illuminate\Routing\Router;

Route::group([
	'namespace' => 'Poppy\System\Http\Request\Develop',
], function (Router $router) {
	/* Pam
	 * ---------------------------------------- */
	$router->any('logout', 'PamController@logout')
		->name('system:develop.pam.logout');

	/* Control
	 * ---------------------------------------- */
	$router->get('api', 'CpController@api')
		->name('system:develop.cp.api');
	$router->any('set_token', 'CpController@setToken')
		->name('system:develop.cp.set_token');
	$router->any('api_login', 'CpController@apiLogin')
		->name('system:develop.cp.api_login');
	$router->get('doc/{type?}', 'CpController@doc')
		->name('system:develop.cp.doc');

	/* Env
	 * ---------------------------------------- */
	$router->get('env/phpinfo', 'EnvController@phpinfo')
		->name('system:develop.env.phpinfo');
	$router->get('env/db', 'EnvController@db')
		->name('system:develop.env.db');
	$router->get('env/model', 'EnvController@model')
		->name('system:develop.env.model');
	$router->get('env/config/{path?}', 'EnvController@config')
		->name('system:develop.env.config');

	/* Log
	 * ---------------------------------------- */
	$router->any('log', 'LogController@index')
		->name('system:develop.log.index');

	/* ApiDoc
	 * ---------------------------------------- */
	$router->any('api_doc/field/{type}/{field}', 'ApiDocController@field')
		->name('system:develop.doc.field');
	$router->any('api_doc/{type?}', 'ApiDocController@auto')
		->name('system:develop.doc.index');

	/* Layout
	 * ---------------------------------------- */
	$router->any('layout/fe', 'LayoutController@fe')
		->name('system:develop.layout.fe');
	$router->any('l/{page?}', 'LayoutController@index')
		->name('system:develop.layout.index');
	$router->any('mail/{slug}/{page?}', 'LayoutController@mail')
		->name('system:develop.layout.mail');

	// progress
	$router->any('progress', 'ProgressController@index')
		->name('system:develop.progress.index');
	$router->any('progress/lists', 'ProgressController@lists')
		->name('system:develop.progress.lists');
});
