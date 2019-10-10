<?php namespace Poppy\System\Http;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * Define the routes for the module.
	 * @return void
	 */
	public function map(): void
	{
		$this->mapWebRoutes();

		$this->mapDevRoutes();

		$this->mapApiRoutes();
	}

	/**
	 * Define the "web" routes for the module.
	 * These routes all receive session state, CSRF protection, etc.
	 * @return void
	 */
	protected function mapWebRoutes(): void
	{
		Route::group([
			'middleware' => 'web',
			'prefix'     => 'system',
		], function () {
			require_once __DIR__ . '/Routes/web.php';
		});

		// backend
		Route::group([
			'prefix' => 'backend',
		], function (Router $router) {
			$router->any('/', config('poppy.backend_cp') ?: 'Poppy\System\Http\Request\Backend\HomeController@index')
				->middleware('backend-auth')
				->name('system:backend.home.index');
			$router->any('login', 'Poppy\System\Http\Request\Backend\HomeController@login')
				->middleware('web')
				->name('system:backend.home.login');
		});

		Route::group([
			'prefix'     => 'backend/system',
			'middleware' => 'backend-auth',
		], function () {
			require_once __DIR__ . '/Routes/backend.php';
		});

		/* Mobile
		 * ---------------------------------------- */
		Route::group([
			'middleware' => 'web',
			'prefix'     => 'mobile/system',
		], function () {
			require_once __DIR__ . '/Routes/mobile.php';
		});
	}

	/**
	 * Define the "web" routes for the module.
	 * These routes all receive session state, CSRF protection, etc.
	 * @return void
	 */
	protected function mapDevRoutes(): void
	{
		// develop
		Route::group([
			'middleware' => 'web',
			'prefix'     => 'develop',
		], function (Router $router) {
			$router->any('login', 'Poppy\System\Http\Request\Develop\PamController@login')
				->name('system:develop.pam.login');
			$router->get('/', 'Poppy\System\Http\Request\Develop\CpController@index')
				->middleware('develop-auth')
				->name('system:develop.cp.cp');
		});
		Route::group([
			'middleware' => 'develop-auth',
			'prefix'     => 'develop/system',
		], function () {
			require_once __DIR__ . '/Routes/develop.php';
		});
	}

	/**
	 * Define the "api" routes for the module.
	 * These routes are typically stateless.
	 * @return void
	 */
	protected function mapApiRoutes(): void
	{
		// Api V1 版本
		Route::group([
			'middleware' => ['site_open'],
			'prefix'     => 'api_v1',
		], function () {
			require_once __DIR__ . '/Routes/api_v1_web.php';
		});
	}
}