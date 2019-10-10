<?php namespace Poppy\System\Commands;

use Illuminate\Console\Command;
use Mail;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Poppy\System\Classes\Inspect\ApiParser;
use Poppy\System\Mail\MaintainMail;
use Throwable;

/**
 * User
 */
class MaintainCommand extends Command
{
	/**
	 * @var string 名称
	 */
	protected $name = 'system:maintain';

	/**
	 * @var string 描述
	 */
	protected $description = 'System Maintain Tool.';

	/**
	 * @var array 检测的环境结果
	 */
	private $env = [];

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$do = $this->argument('do');
		switch ($do) {
			case 'request':
				$this->inspectRequest();
				break;
			case 'env':
				$this->envExt();
				$this->envEnv();
				if (count($this->env)) {
					$this->error('Env Must Set:');
					$this->table(['Type', 'Description'], $this->env);
				}
				$this->info('Your Env Set Are Perfected');
				break;
			case 'mail':
				$title   = $this->option('title') ?: 'No Title';
				$content = $this->option('content') ?: 'No Content';
				$file    = $this->option('file');
				try {
					Mail::to(env('OP_MAIL'))->send(new MaintainMail($title, $content, $file));
				} catch (Throwable $e) {
					$this->error(sys_error('system', self::class, $e->getMessage()));
				}
				break;
			default:
				$this->warn('Error type in maintain tool.');
				break;
		}
	}

	/**
	 * 服务器请求函数用来验证是否正确
	 * 是否是匹配的注释(延后)
	 */
	private function inspectRequest(): void
	{
		$type       = $this->option('type');
		$displayLog = $this->option('log');
		$url        = $this->option('url');

		try {
			$Parser = new ApiParser($type, $url);

			$this->info('Request Type : ' . $type . ', Token:' . $Parser->token());
			foreach ($Parser->getDefinition() as $definition) {
				if (!$Parser->sendRequest($definition)) {
					$this->warn($Parser->getError());
				}
				else {
					$this->info($Parser->getSuccess());
				}
				$logs = $Parser->getCurrentLog();

				if ($displayLog || $logs['status_code'] !== 200) {
					$log = collect($logs)->map(function ($value, $key) {
						return [
							'key'   => $key,
							'value' => $value,
						];
					});
					$this->table(['说明', '进展'], $log);
				}
				usleep(100000);
			}
		} catch (Throwable $e) {
			$this->error($e->getMessage());
		}
	}

	private function envExt(): void
	{
		$extensions = [
			'bcmath',
			'gd',
			'mbstring',
			'pdo',
			'xml',
			'json',
		];
		foreach ($extensions as $extension) {
			if (!extension_loaded($extension)) {
				$this->env[] = [
					'php-ext',
					$extension . ' not loaded',
				];
			}
		}
	}

	private function envEnv(): void
	{
		$envs = [
			['APP_ENV', 'App 运行环境', ['local', 'production']],
			['APP_DEBUG', '是否启用 Debug 模式'],
			['APP_KEY', 'Session / Cookie 加密 Key'],
			['URL_SITE', '线上地址'],

			['DB_HOST', '数据库主机地址'],
			['DB_DATABASE', '数据库名称'],
			['DB_USERNAME', '数据库用户名'],
			['DB_PASSWORD', '数据库密码'],

			['CACHE_DRIVER', '缓存驱动', ['file', 'redis']],
			['SESSION_DRIVER', 'Session 驱动', ['file', 'redis']],
			['CACHE_PREFIX', '缓存前缀'],
			['QUEUE_DRIVER', '队列驱动', ['database', 'redis', 'sync']],

			['JWT_SECRET', 'Jwt Token'],
		];
		foreach ($envs as $env) {
			$key     = $env[0] ?? '';
			$desc    = $env[1] ?? '';
			$allowed = $env[2] ?? [];
			if (!env($key)) {
				if (count($allowed)) {
					$appendStr = 'allowed:' . implode(',', $allowed);
				}
				$this->env[] = [
					'env',
					$key . '(' . $desc . ') must set.' . ($appendStr ?? ''),
				];
			}
		}
	}

	protected function getArguments(): array
	{
		return [
			['do', InputArgument::REQUIRED, 'Maintain type.'],
		];
	}

	protected function getOptions(): array
	{
		return [
			['title', null, InputOption::VALUE_OPTIONAL, 'Mail Title'],
			['content', null, InputOption::VALUE_OPTIONAL, 'Mail Content'],
			['file', null, InputOption::VALUE_OPTIONAL, 'Mail Content'],
			['log', null, InputOption::VALUE_NONE, 'Need Log'],
			['type', null, InputOption::VALUE_OPTIONAL, 'Request Type'],
			['url', null, InputOption::VALUE_OPTIONAL, 'Request Url'],
		];
	}
}