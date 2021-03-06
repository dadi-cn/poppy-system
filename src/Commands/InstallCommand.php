<?php

namespace Poppy\System\Commands;

use Illuminate\Console\Command;
use Poppy\System\Models\PamRole;

/**
 * 项目初始化
 */
class InstallCommand extends Command
{
	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'py-system:install';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'Install system module.';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		// check
		if (PamRole::where('name', PamRole::BE_ROOT)->exists()) {
			$this->warn('You Already Installed!');

			return;
		}

		$this->line('Start Install Lemon Framework!');

		/* Role
		 -------------------------------------------- */
		$this->warn('Init UserRole Ing...');
		$this->call('py-system:user', [
			'do' => 'init_role',
		]);
		$this->info('Install User Roles Success');

		/* permission
		 -------------------------------------------- */
		$this->warn('Init Rbac Permission...');
		$this->call('py-core:permission', [
			'do' => 'init',
		]);
		$this->info('Init Rbac Permission Success');
	}
}