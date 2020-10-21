<?php namespace Poppy\System;

/**
 * Copyright (C) Update For IDE
 */

use Auth;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Illuminate\Auth\Events\Failed as AuthFailEvent;
use Illuminate\Auth\Events\Login as AuthLoginEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Poppy\Core\Events\PermissionInitEvent;
use Poppy\Framework\Support\PoppyServiceProvider;
use Poppy\System\Classes\Auth\Guard\JwtAuthGuard;
use Poppy\System\Classes\Auth\Provider\BackendProvider;
use Poppy\System\Classes\Auth\Provider\DevelopProvider;
use Poppy\System\Classes\Auth\Provider\PamProvider;
use Poppy\System\Classes\Auth\Provider\WebProvider;
use Poppy\System\Classes\FormBuilder;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\Policies\PamAccountPolicy;
use Poppy\System\Models\Policies\PamRolePolicy;

/**
 * @property $listens;
 */
class ServiceProvider extends PoppyServiceProvider
{
	/**
	 * @var string Module name
	 */
	protected $name = 'poppy.system';

	protected $listens = [
		// laravel
		AuthLoginEvent::class           => [

		],
		PermissionInitEvent::class      => [
			Listeners\PermissionInit\InitToDbListener::class,
		],
		AuthFailEvent::class            => [
			Listeners\AuthFailed\LogListener::class,
		],

		// system
		Events\LoginSuccessEvent::class => [
			Listeners\LoginSuccess\LogListener::class,
			Listeners\LoginSuccess\UpdateLastLoginListener::class,
		],
	];

	protected $policies = [
		PamRole::class    => PamRolePolicy::class,
		PamAccount::class => PamAccountPolicy::class,
	];

	/**
	 * Bootstrap the module services.
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(dirname(__DIR__) . '/resources/views', 'poppy-system');
		$this->loadTranslationsFrom(dirname(__DIR__) . '/resources/lang', 'poppy-system');
		$this->loadMigrationsFrom(dirname(__DIR__) . '/src/Databases/Migrations');

		if ($this->listens) {
			$this->bootListener();
		}

		if ($this->policies) {
			$this->bootPolicies();
		}

		// 注册 api 文档配置
		$this->publishes([
			__DIR__ . '/../resources/config/system.php'                       => base_path('config/poppy.php'),
			__DIR__ . '/../resources/images/system/spacer.gif'                => public_path('assets/images/system/spacer.gif'),
			__DIR__ . '/../resources/views/vendor/pagination-layui.blade.php' => resource_path('views/vendor/pagination/layui.blade.php'),
		], 'poppy-system');

		// 配置文件
		$this->mergeConfigFrom(dirname(__DIR__) . '/resources/config/system.php', 'poppy');

		$this->bootConfigMail();
	}

	/**
	 * Register the module services.
	 * @return void
	 */
	public function register()
	{
		$this->app->register(Http\MiddlewareServiceProvider::class);
		$this->app->register(Http\RouteServiceProvider::class);
		$this->app->register(Setting\SettingServiceProvider::class);

		$this->registerConsole();

		$this->registerAuth();

		$this->registerSchedule();
	}

	public function provides(): array
	{
		return [
			'system.form',
		];
	}

	private function registerSchedule()
	{
		app('events')->listen('console.schedule', function (Schedule $schedule) {
			$schedule->command('system:user', ['auto_enable'])
				->everyFiveMinutes()->appendOutputTo($this->consoleLog());
			$schedule->command('system:user', ['clear_log'])
				->everyFiveMinutes()->appendOutputTo($this->consoleLog());

			// 开发平台去生成文档
			if (env('APP_ENV', 'production') !== 'production') {
				// 自动生成文档
				$schedule->command('system:doc api')
					->everyMinute()->appendOutputTo($this->consoleLog());

				// auto clean
				$schedule->command('clockwork:clean')
					->everyThirtyMinutes()->appendOutputTo($this->consoleLog());
			}
		});
	}

	private function registerConsole()
	{
		// system
		$this->commands([
			// system:module
			Commands\UserCommand::class,
			Commands\InstallCommand::class,
		]);
	}

	private function registerAuth()
	{
		Auth::provider('pam.web', function ($app) {
			return new WebProvider(PamAccount::class);
		});
		Auth::provider('pam.backend', function ($app) {
			return new BackendProvider(PamAccount::class);
		});
		Auth::provider('pam.develop', function ($app) {
			return new DevelopProvider(PamAccount::class);
		});
		Auth::provider('pam', function ($app) {
			return new PamProvider(PamAccount::class);
		});

		Auth::extend('jwt.backend', function (Application $app, $name, array $config) {
			$guard = new JwtAuthGuard(
				$app['tymon.jwt'],
				$app['auth']->createUserProvider($config['provider']),
				$app['request']
			);
			$app->refresh('request', $guard, 'setRequest');

			return $guard;
		});

		$this->app->singleton('system.form', function ($app) {
			$form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());

			return $form->setSessionStore($app['session.store']);
		});
	}

	private function bootConfigMail()
	{
		config([
			'mail.driver'       => sys_setting('system::mail.driver') ?: config('mail.driver'),
			'mail.encryption'   => sys_setting('system::mail.encryption') ?: config('mail.encryption'),
			'mail.port'         => sys_setting('system::mail.port') ?: config('mail.port'),
			'mail.host'         => sys_setting('system::mail.host') ?: config('mail.host'),
			'mail.from.address' => sys_setting('system::mail.from') ?: config('mail.from.address'),
			'mail.from.name'    => sys_setting('system::mail.from') ?: config('mail.from.name'),
			'mail.username'     => sys_setting('system::mail.username') ?: config('mail.username'),
			'mail.password'     => sys_setting('system::mail.password') ?: config('mail.password'),
		]);
	}
}