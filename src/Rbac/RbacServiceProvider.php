<?php namespace Poppy\System\Rbac;

/**
 * Copyright (C) Update For IDE
 */

use Blade;
use Illuminate\Support\ServiceProvider;


class RbacServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the module services.
	 */
	public function boot()
	{
		// rbac
		$this->bootRbacBladeDirectives();
	}

	/**
	 * Register the module services.
	 * @return void
	 */
	public function register()
	{
		$this->registerRbac();
	}

	/**
	 * register rbac and alias
	 */
	private function registerRbac()
	{
		$this->app->bind('system.rbac', function ($app) {
			return new Rbac($app);
		});
		$this->app->alias('system.rbac', Rbac::class);
	}

	/**
	 * Register the blade directives
	 * @return void
	 */
	private function bootRbacBladeDirectives()
	{
		// Call to Entrust::hasRole
		Blade::directive('role', function ($expression) {
			return "<?php if (\\Rbac::hasRole({$expression})) : ?>";
		});

		Blade::directive('endrole', function ($expression) {
			return '<?php endif; // Rbac::hasRole ?>';
		});

		// Call to Entrust::capable
		Blade::directive('permission', function ($expression) {
			return "<?php if (\\Rbac::capable({$expression})) : ?>";
		});

		Blade::directive('endpermission', function ($expression) {
			return '<?php endif; // Rbac::capable ?>';
		});

		// Call to Entrust::ability
		Blade::directive('ability', function ($expression) {
			return "<?php if (\\Rbac::ability({$expression})) : ?>";
		});

		Blade::directive('endability', function ($expression) {
			return '<?php endif; // Rbac::ability ?>';
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'system.rbac',
		];
	}
}
