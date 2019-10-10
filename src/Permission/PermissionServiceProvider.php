<?php namespace Poppy\System\Permission;

use Illuminate\Support\ServiceProvider;

/**
 * Class PermissionServiceProvider.
 */
class PermissionServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * ServiceProvider register.
	 */
	public function register()
	{
		$this->app->singleton('permission', function ($app) {
			return new PermissionManager();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'permission',
		];
	}
}
