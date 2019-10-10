<?php namespace Poppy\System\Module;

use Illuminate\Support\ServiceProvider;

/**
 * Class ModuleServiceProvider.
 */
class ModuleServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register for service provider.
	 */
	public function register()
	{
		$this->app->singleton('module', function ($app) {
			return new ModuleManager();
		});
	}

	/**
	 * @return array
	 */
	public function provides(): array
	{
		return ['module'];
	}
}
