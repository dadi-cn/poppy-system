<?php namespace Poppy\System\Setting;

use Illuminate\Support\ServiceProvider;
use Poppy\System\Setting\Repository\SettingRepository;

class SettingServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * @return array
	 */
	public function provides()
	{
		return ['setting'];
	}

	/**
	 * Register for service provider.
	 */
	public function register()
	{
		$this->app->singleton('setting', function () {
			return new SettingRepository();
		});
	}
}
