<?php

use Illuminate\Routing\Router;

Route::group([
	'namespace' => 'Poppy\System\Http\Request\Backend',
], function (Router $router) {
	$router->any('cp', 'HomeController@cp')
		->name('system:backend.home.cp');
	$router->any('password', 'HomeController@password')
		->name('system:backend.home.password');
	$router->any('fe/{type?}', 'HomeController@fe')
		->name('system:backend.home.fe');
	$router->any('sample/page', 'HomeController@samplePage');
	$router->any('sample/list', 'HomeController@sampleList');
	$router->any('logout', 'HomeController@logout')
		->name('system:backend.home.logout');
	$router->any('setting/{path?}', 'HomeController@setting')
		->name('system:backend.home.setting');
	$router->any('easy-web/{type}', 'HomeController@easyWeb')
		->name('system:backend.home.easy-web');

	$router->get('role', 'RoleController@index')
		->name('system:backend.role.index');
	$router->any('role/establish/{id?}', 'RoleController@establish')
		->name('system:backend.role.establish');
	$router->any('role/delete/{id?}', 'RoleController@delete')
		->name('system:backend.role.delete');
	$router->any('role/menu/{id}', 'RoleController@menu')
		->name('system:backend.role.menu');

	$router->get('pam', 'PamController@index')
		->name('system:backend.pam.index');
	$router->any('pam/establish/{id?}', 'PamController@establish')
		->name('system:backend.pam.establish');
	$router->any('pam/password/{id}', 'PamController@password')
		->name('system:backend.pam.password');
	$router->any('pam/disable/{id}', 'PamController@disable')
		->name('system:backend.pam.disable');
	$router->any('pam/enable/{id}', 'PamController@enable')
		->name('system:backend.pam.enable');
	$router->any('pam/log', 'PamController@log')
		->name('system:backend.pam.log');
	/* addon
	 * ---------------------------------------- */
	$router->any('addon', 'AddonController@index')
		->name('system:backend.addon.index');
	$router->any('addon/config/{folder?}', 'AddonController@config')
		->name('system:backend.addon.config')
		->where('folder', '[a-zA-Z\/]+');

	// 短信模版配置
	$router->get('sms', 'SmsController@index')
		->name('system:backend.sms.index');
	$router->any('sms/establish/{id?}', 'SmsController@establish')
		->name('system:backend.sms.establish');
	$router->any('sms/destroy/{id}', 'SmsController@destroy')
		->name('system:backend.sms.destroy');

	/* 发送测试邮件
	 * ---------------------------------------- */
	$router->any('mail/store', 'MailController@store')
		->name('system:backend.mail.store');
	$router->any('mail/test', 'MailController@test')
		->name('system:backend.mail.test');
});