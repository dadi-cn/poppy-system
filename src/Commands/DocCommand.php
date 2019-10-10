<?php namespace Poppy\System\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

/**
 * 使用命令行生成 api 文档
 */
class DocCommand extends Command
{

	protected $signature = 'system:doc
		{type : Document type to run. [api]}
	';

	protected $description = 'Generate Api Doc Document';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$type = $this->argument('type');
		switch ($type) {
			case 'api':
				if (!command_exist('apidoc')) {
					$this->error("apidoc 命令不存在\n");
				}
				else {
					$catalog = config('module.system.apidoc');
					if (!$catalog) {
						$this->error('尚未配置 apidoc 生成目录');

						return;
					}
					// 多少个任务
					$bar = $this->output->createProgressBar(count($catalog));

					foreach ($catalog as $key => $dir) {
						$this->performTask($key);
						// 一个任务处理完了，可以前进一点点了
						$bar->advance();
					}
					$bar->finish();
				}
				break;
			case 'mobile':
				if (!command_exist('apidoc')) {
					$this->error("apidoc 命令不存在\n");
				}
				else {
					$path = base_path('modules');
					$aim  = base_path('public/docs/mobile');

					if (!file_exists($path)) {
						$this->error('Err > 目录 `' . $path . '` 不存在');

						return;
					}
					$f     = ' -f ' . 'request/mobile/.*\.php$';
					$shell = 'apidoc -i ' . $path . '  -o ' . $aim . ' ' . $f;
					$this->info($shell);
					$process = new Process($shell);
					$process->start();
					$process->wait(function ($type, $buffer) {
						if (Process::ERR === $type) {
							$this->error('ERR > ' . $buffer . " [mobile]\n");
						}
					});
				}
				break;
			case 'phpcs':
			case 'cs':
				$this->info(
					'Please Run Command:' . "\n" .
					'php-cs-fixer fix --config=' . pf_path('.php_cs') . ' --diff --dry-run --verbose --diff-format=udiff'
				);
				break;
			case 'cs-pf':
				$this->info(
					'Please Run Command:' . "\n" .
					'php-cs-fixer fix ' . pf_path('') . ' --config=' . pf_path('.php_cs') . ' --diff --dry-run --verbose --diff-format=udiff'
				);
				break;
			case 'phplint':
			case 'lint':
				$lintFile = base_path('vendor/bin/phplint');
				if (file_exists($lintFile)) {
					$this->info(
						'Please Run Command:' . "\n" .
						'phplint ' . base_path() . ' -c ' . pf_path('.phplint.yml')
					);
				}
				else {
					$this->warn('Please run `composer require overtrue/phplint -vvv` to install phplint');
				}
				break;
			case 'php':
			case 'sami':
				$sami       = storage_path('sami/sami.phar');
				$samiConfig = storage_path('sami/config.php');
				if (!file_exists($samiConfig)) {
					$this->warn(
						'Please Run Command To Publish Config:' . "\n" .
						'php artisan vendor:publish '
					);

					return;
				}
				if (file_exists($sami)) {
					$this->info(
						'Please Run Command:' . "\n" .
						'php ' . $sami . ' update ' . $samiConfig
					);
				}
				else {
					$this->warn(
						'Please Run Command To Install Sami.phar:' . "\n" .
						'curl http://get.sensiolabs.org/sami.phar --output ' . $sami
					);
				}
				break;
			case 'fw':
				$sami       = storage_path('sami/sami.phar');
				$samiConfig = storage_path('sami/fw.php');
				if (!file_exists($samiConfig)) {
					$this->warn(
						'Please Run Command To Publish Config:' . "\n" .
						'php artisan vendor:publish '
					);

					return;
				}
				if (file_exists($sami)) {
					$this->info(
						'Please Run Command:' . "\n" .
						'php ' . $sami . ' update ' . $samiConfig
					);
				}
				else {
					$this->warn(
						'Please Run Command To Install Sami.phar:' . "\n" .
						'curl http://get.sensiolabs.org/sami.phar --output ' . $sami
					);
				}
				break;
			case 'log':
				$this->info(
					'Please Run Command:' . "\n" .
					'tail -20f storage/logs/laravel-`date +%F`.log'
				);
				break;
			default:
				$this->comment('Type is now allowed.');
				break;
		}
	}

	/**
	 * @param string $key 需要处理的 key
	 */
	private function performTask($key)
	{
		$path = base_path('modules');
		$aim  = base_path('public/docs/' . $key);

		if (!file_exists($path)) {
			$this->error('Err > 目录 `' . $path . '` 不存在');

			return;
		}
		$f     = ' -f ' . 'request/api.*/' . $key . '/.*\.php$';
		$lower = strtolower($key);
		$shell = 'apidoc -i ' . $path . '  -o ' . $aim . ' ' . $f;
		$this->info($shell);
		$process = new Process($shell);
		$process->start();
		$process->wait(function ($type, $buffer) use ($lower) {
			if (Process::ERR === $type) {
				$this->error('ERR > ' . $buffer . " [$lower]\n");
			}
		});
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['type', InputArgument::REQUIRED, ' Support Type [api,phpcs|cs,log,php|sami,lint|phplint].'],
		];
	}
}