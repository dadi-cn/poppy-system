<?php namespace Poppy\System\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Poppy\System\Classes\Inspect\CommentParser;
use Poppy\System\Models\PamPermission;
use Throwable;

/**
 * 检查代码规则
 */
class InspectCommand extends Command
{
	use KeyParserTrait;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'system:inspect
		{type? : Support type need to input, [apidoc, method, file, db, env]}
		{--dir= : The dir to check with directory}
		{--module= : The module to check}
		{--class_load_only : Only load class with not show tables}
		{--log : Is Display Request Log}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Inspect code style';

	/**
	 * @var array  File Rules
	 */
	private $fileRules = [];

	/**
	 * @var array Name Rules
	 */
	private $nameRules = [];

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$type = $this->argument('type');
		switch ($type) {
			case 'apidoc':
				$dir = $this->option('dir');
				if (!$dir) {
					$this->warn('You need point the directory to check!');

					return;
				}
				$this->inspectApidoc();
				break;
			case 'class':
				$this->inspectClass();
				break;
			case 'pages':
				$this->inspectPages();
				break;
			case 'file':
			case 'name':
				$this->inspectFile();
				break;
			case 'db':
			case 'database':
				$this->inspectDatabase();
				break;
			case 'controller':
				$this->inspectController();
				break;
			case 'action':
				$this->inspectAction();
				break;
			case 'seo':
				$this->inspectSeo();
				break;
			case 'db_seo':
				$this->inspectDbSeo();
				break;
			case 'check_perms':
				$this->checkWebPerms();
				break;
			default:
				$dirs = array_keys(config('module.system.apidoc'));
				if (count($dirs)) {
					foreach ($dirs as $dir) {
						$this->call('system:inspect', [
							'type'  => 'apidoc',
							'--dir' => $dir,
						]);
					}
				}

				// batch seo
				$modules = app('module')->enabled()->keys();
				$modules->each(function ($module) {
					$this->call('system:inspect', [
						'type'     => 'seo',
						'--module' => $module,
					]);
				});

				$this->call('system:inspect', [
					'type' => 'name',
				]);

				$this->call('system:inspect', [
					'type' => 'db',
				]);

				$this->call('system:inspect', [
					'type' => 'class',
				]);

				$this->call('system:inspect', [
					'type' => 'pages',
				]);
				break;
		}
	}

	private function inspectPages()
	{
		$pages   = app('module')->pages();
		$collect = collect();
		$pages->each(function ($item, $slug) use ($collect) {
			$item['tabs']->each(function ($item) use ($slug, $collect) {
				$item['fields']->each(function ($item) use ($slug, $collect) {
					$parsed = $this->parseKey($item['key']);
					$slug   = substr($slug, strpos($slug, '.') + 1);
					if (($parsed[0] ?? '') !== $slug) {
						$collect->push([
							$slug,
							$item['key'],
							$item['label'],
							str_limit(sys_setting($item['key']), 20),
						]);
					}
				});
			});
		});
		if ($collect->count()) {
			$this->warn('Inspect Pages Key:');
			$this->table(['Module', 'Key', 'Description', 'Value'], $collect);
		}
		else {
			$this->info('Pages key are matched');
		}
	}

	/**
	 * 生成 seo 项目
	 */
	private function inspectSeo()
	{
		$module = $this->option('module');
		if (!$module) {
			/** @var Collection $modules */
			$modules = app('poppy')->enabled();
			$modules = $modules->pluck('slug');
		}
		else {
			$modules = collect($module);
		}
		$seoList = [];
		$modules->each(function ($module) use (&$seoList) {
			collect(\Route::getRoutes())->map(function (Route $route) use ($module, &$seoList) {
				$name = $route->getName();
				if (starts_with($name, $module)) {
					$seoKey   = str_replace([':', '.', '::'], ['::', '_', '::seo.'], $name);
					$transKey = trans($seoKey);
					if ($transKey === $seoKey || $transKey === '') {
						$seoList[] = [
							$module,
							'\'' . str_replace([$module . ':', '.'], ['', '_'], $name) . '\' => \'\', ',
						];
					}
				}
			});
		});

		$this->warn('[Inspect:' . ucfirst($module) . ' Seo]');
		if ($seoList) {
			$this->table(['Module', 'Key'], $seoList);
		}
		else {
			$this->info('Perfect, Seo rule are matched');
		}
	}

	/**
	 * 检查网页权限
	 */
	private function checkWebPerms()
	{
		/** @var Collection $webPerms */
		$webPerms = app('module')->webMenus()->routePerm();
		$perms    = collect();
		$webPerms->each(function ($perm) use ($perms) {
			if (!PamPermission::where('name', $perm)->exists()) {
				$perms->push([$perm]);
			}
		});
		$this->table(['Permission'], $perms);
	}

	/**
	 * 检查数据库配置
	 */
	private function inspectDatabase()
	{
		$tables = array_map('reset', DB::select('show tables'));

		$suggestString   = function ($col) {
			if (strpos($col['Type'], 'char') !== false) {
				if ($col['Null'] === 'YES') {
					return '(Char-null)';
				}
				if ($col['Default'] !== '' && $col['Default'] !== null) {
					if (!is_string($col['Default'])) {
						return '(Char-default)';
					}
				}
			}

			return '';
		};
		$suggestInt      = function ($col) {
			if (strpos($col['Type'], 'int') !== false) {
				switch ($col['Key']) {
					case 'PRI':
						// 主键不能为Null (Allow Null 不可选)
						// Default 不可填入值
						// 所以无任何输出
						break;
					default:
						if (!is_numeric($col['Default'])) {
							return '(Int-default)';
						}
						if ($col['Null'] === 'YES') {
							return '(Int-Null)';
						}
						break;
				}
			}

			return '';
		};
		$suggestDecimal  = function ($col) {
			if (strpos($col['Type'], 'decimal') !== false) {
				if ($col['Default'] !== '0.00') {
					return '(Decimal-default)';
				}
				if ($col['Null'] === 'YES') {
					return '(Decimal-Null)';
				}
			}

			return '';
		};
		$suggestDatetime = function ($col) {
			if (strpos($col['Type'], 'datetime') !== false) {
				if ($col['Default'] !== null) {
					return '(Datetime-default)';
				}
				if ($col['Null'] === 'NO') {
					return '(Datetime-null)';
				}
			}

			return '';
		};
		$suggestFloat    = function ($col) {
			if (strpos($col['Type'], 'float') !== false) {
				return '(Float-set)';
			}

			return '';
		};

		$suggest = [];
		foreach ($tables as $table) {
			$columns = DB::select('show full columns from ' . $table);
			/*
			 * column 字段
			 * Field      : account_no
			 * Type       : varchar(100)
			 * Collation  : utf8_general_ci
			 * Null       : NO
			 * Key        : ""
			 * Default    : ""
			 * Extra      : ""
			 * Privileges : select,insert,update,references
			 * Comment    : 账号
			 * ---------------------------------------- */

			foreach ($columns as $column) {
				$column     = (array) $column;
				$colSuggest =
					$suggestString($column) .
					$suggestInt($column) .
					$suggestDecimal($column) .
					$suggestDatetime($column);
				$suggestFloat($column);
				$column['suggest'] = $colSuggest;
				if ($colSuggest) {
					$suggest[] = [
						$table,
						data_get($column, 'Field'),
						data_get($column, 'Type'),
						data_get($column, 'Null'),
						data_get($column, 'suggest'),
						data_get($column, 'Comment'),
					];
				}
			}
		}
		if ($suggest) {
			$this->table(['Table', 'Field', 'Type', 'IsNull', 'Advice', 'Comment'], $suggest);
		}
	}

	/**
	 * Apidoc 检测
	 */
	private function inspectApidoc()
	{
		$dir         = $this->option('dir');
		$file        = app('files');
		$appendCheck = (array) config('module.system.apidoc.' . $dir . '.check');
		$jsonFile    = base_path('public/docs/' . $dir . '/api_data.json');
		if (!$file->exists($jsonFile)) {
			$this->warn('ApiDoc not exist, run `php artisan system:doc api` to generate.');

			return;
		}
		$funToTable = function ($field, $type, $url) {
			$field['description'] = strip_tags($field['description']);

			return [
				$url,
				$field['field'],
				$field['type'],
				$type,
				$field['description'],
			];
		};

		$match = function ($field, $url, $type, &$table) use ($funToTable, $appendCheck) {
			$match = [
					'id'           => 'Integer',
					'title'        => 'String',
					'page'         => 'Integer',
					'pagesize'     => 'Integer',
					'start_at'     => 'Date',
					'end_at'       => 'Date',
					'created_at'   => 'String',
					'success_at'   => 'String',
					'failed_at'    => 'String',
					'amount'       => 'String',
					'status'       => 'String',
					'app_id'       => 'String',
					'app_key'      => 'String',
					'fee'          => 'String',
					'note'         => 'String',
					'*_at'         => 'String',
					'*_reason'     => 'String',
					'*_no'         => 'String',
					'*_id'         => 'Integer',
					'*_success_at' => 'String',
					'*_failed_at'  => 'String',
				] + $appendCheck;

			foreach ($match as $item_key => $item) {
				if ($field['field'] === $item_key && $field['type'] !== $item) {
					$table[] = $funToTable($field, $type, $url);
					continue;
				}
				if (strpos($item_key, '*') !== false) {
					if (array_key_exists($field['field'], $match)) {
						continue;
					}
					$string = ltrim($item_key, '*');
					if ($field['type'] !== $item && str_contains($field['field'], $string)) {
						$table[] = $funToTable($field, $type, $url);
					}
					continue;
				}
			}
		};

		try {
			$arrApi = json_decode($file->get($jsonFile), true);
		} catch (FileNotFoundException $e) {
			$this->warn($e->getMessage());

			return;
		}
		$table = [];
		foreach ($arrApi as $api) {
			$url = $api['url'];

			$params    = data_get($api, 'parameter.fields.Parameter');
			$successes = data_get($api, 'success.fields.Success 200');

			if ($params && count($params)) {
				foreach ($params as $param) {
					$match($param, $url, 'param', $table);
				}
			}

			if ($successes && count($successes)) {
				foreach ($successes as $success) {
					$match($success, $url, 'success', $table);
				}
			}
		}

		$this->warn('[Inspect:Apidoc:' . $dir . ']');
		if (count($table)) {
			$this->table(['Url', 'Field', 'Type', 'Input/Output', 'Description'], $table);
		}
		else {
			$this->info('Apidoc Comment Check Success!');
		}
	}

	/**
	 * 方法检测
	 */
	private function inspectClass()
	{
		$optClassLoadOnly = $this->option('class_load_only');

		$baseDir = base_path();
		$folders = array_merge(
			glob($baseDir . '/{extensions,modules}/*/src', GLOB_BRACE),
			glob($baseDir . '/{framework}/src', GLOB_BRACE)
		);

		$table      = [];
		$classTable = [];
		foreach ($folders as $dir) {
			$files = app('files')->allFiles($dir);
			foreach ($files as $file) {
				$pathName   = $file->getPathname();
				$moduleName = $this->moduleName($pathName);

				// 排除指定的类
				if (str_contains($pathName, [
					'database/', 'update/', 'functions.php', 'ServiceProvider',
					'http/routes/', '.sql', '.txt', '.pem', '.xml', '.md', '.yaml', '.table', '.stub',
				])) {
					continue;
				}

				// 模块名称解析错误
				if (!$moduleName) {
					// module name not check with extension
					if (str_contains($pathName, 'extensions')) {
						continue;
					}
					$this->warn('Error module name in path:' . $pathName);

					return;
				}

				$relativePath = $file->getRelativePath();
				$className    = $this->className($moduleName, $relativePath, $file->getFilename());
				if (str_contains($className, 'Framework')) {
					$className = 'Poppy\\' . $className;
				}
				try {
					$refection = new ReflectionClass($className);
				} catch (Throwable $e) {
					$classTable[] = [
						$moduleName,
						$e->getMessage(),
					];
					continue;
				}

				$properties = $refection->getProperties();
				$varDesc    = [];
				foreach ($properties as $property) {
					if ($property->class !== $className) {
						continue;
					}
					// action variable do not need
					if (strpos($className, '\\Models\\') !== false && in_array($property->getName(), [
							'timestamps', 'table', 'fillable', 'primaryKey', 'dates',
						], true)) {
						continue;
					}
					if (strpos($className, '\\Commands\\') !== false && in_array($property->getName(), [
							'signature', 'description',
						], true)) {
						continue;
					}

					if (!$property->getDocComment()) {
						$varDesc[] = [
							$moduleName,
							'',
							'$' . $property->name,
							'[comment missing]',
						];
					}

					// 检测 CamelCase
					if (!$this->isCamelCase($property->getName())) {
						$varDesc[] = [
							$moduleName,
							'',
							'',
							'param : => ' . '$' . camel_case($property->getName()),
						];
					}

				}
				$methods = $refection->getMethods();
				if ($methods === null) {
					continue;
				}

				$methodDesc = [];

				foreach ($methods as $method) {
					$methodName = $method->getName();
					if (
						in_array($methodName, [
							'handle', '',
						], true)
						&&
						(
							strpos($className, '\\Listeners\\') !== false
							||
							strpos($className, '\\Middlewares\\') !== false
						)) {
						continue;
					}

					// 排除继承的方法
					if ($method->class !== $className) {
						continue;
					}

					// 排除魔术方法
					if (starts_with($methodName, '__')) {
						continue;
					}

					// 不是本文件中的. 略过
					if (basename($method->getFileName()) !== basename($file->getRealPath())) {
						continue;
					}

					// 检查 注释
					$comment = $method->getDocComment();
					$item    = [
						$moduleName,
						'',
						$methodName,
					];
					if (!$comment) {
						$item[]       = '[comment: missing]';
						$methodDesc[] = $item;
					}
					else {
						$Parser      = new CommentParser();
						$parsed      = $Parser->parseMethod($comment);
						$commentDesc = '';
						if (count($parsed['params'])) {
							foreach ($parsed['params'] as $param) {
								$name = $param['var_name'] ?? '';
								if (!$name) {
									continue;
								}

								$desc = $param['var_desc'] ?? '';
								$type = $param['var_type'] ?? '';
								if (!$desc || !$type) {
									$commentDesc    .= "{$name} ";
									$varCommentDesc = '';
									if (!$type) {
										$varCommentDesc .= 'type:' . ($type ?: '--') . ',';
									}
									if (!$desc) {
										$varCommentDesc .= 'desc:' . ($desc ?: '--') . ',';
									}
									$commentDesc .= $varCommentDesc ? '[' . rtrim($varCommentDesc, ',') . ']' : '';
									$commentDesc .= "\n";
								}
							}
						}

						if ($commentDesc) {
							$item[]       = rtrim($commentDesc);
							$methodDesc[] = $item;
						}

						// 代码是否已经审核
						if (isset($parsed['verify'])) {
							$item[]       = '';
							$item[]       = 'method: ' . $methodName . ': verify √';
							$methodDesc[] = $item;
						}
					}

					// 检测 CamelCase
					if (!$this->isCamelCase($methodName)) {
						$methodDesc[] = [
							$moduleName,
							'',
							'',
							'method : => ' . camel_case($methodName),
						];
					}
				}

				$trimComment       = str_replace(['/', '*', "\t", "\n", ' '], '', $refection->getDocComment());
				$docCommentMissing = true;
				if ($trimComment) {
					$docCommentMissing = false;
				}
				if ($docCommentMissing || $varDesc || $methodDesc) {
					$table[] = [
						$moduleName,
						$className,
						'',
						$docCommentMissing ? '[class doc missing]' : '',
					];
					foreach (array_merge($varDesc, $methodDesc) as $item) {
						$table[] = $item;
					}
				}
			}
		}

		if ($optClassLoadOnly) {
			return;
		}

		$this->warn('[Inspect:Comment]');
		if ($table) {
			if ($this->option('module')) {
				$num   = 1;
				$table = collect($table)->filter(function ($item) {
					return stripos($item[0], $this->option('module')) === 0;
				})->map(function ($item) use (&$num) {
					array_unshift($item, $num++);

					return $item;
				})->toArray();
				$this->table(['Id', 'Module', 'Class Name', 'Method', 'Comment'], $table);
			}
			else {
				$this->table(['Module', 'Class Name', 'Method', 'Comment', 'Verify'], $table);
			}
		}
		else {
			$this->info('So good, You did not has bad design.');
		}
		$this->warn('[Inspect:Module Namespace]');
		if ($classTable) {
			$this->table(['Module', 'Tips'], $classTable);
		}
		else {
			$this->info('So good, You did not has bad design in module.');
		}
	}

	/**
	 * 检查文件命名
	 */
	private function inspectFile()
	{
		$baseDir  = base_path();
		$folders  = glob($baseDir . '/{modules}/*/src/{events,listeners,models}', GLOB_BRACE);
		$iterator = Finder::create()
			->files()
			->name('*.php')
			->in($folders);

		$checkFile = function (SplFileInfo $file) {
			$pathName = $file->getPathname();
			$fileName = $file->getFilename();
			$module   = function ($str) {
				if (preg_match('/modules\/(.+)\/src/', $str, $match)) {
					return $match[1];
				}

				return '';
			};
			if (strpos($pathName, '/events/') !== false && substr(pathinfo($fileName)['filename'], -5) !== 'Event') {
				$this->nameRules[] = [
					'module' => $module($pathName),
					'file'   => $fileName,
					'path'   => $pathName,
				];
			}
			if (strpos($pathName, '/listeners/') !== false && substr(pathinfo($fileName)['filename'], -8) !== 'Listener') {
				$this->nameRules[] = [
					'module' => $module($pathName),
					'file'   => $fileName,
					'path'   => $pathName,
				];
			}

			if (strpos($pathName, '/policies/') !== false) {
				if (substr(pathinfo($fileName)['filename'], -6) !== 'Policy') {
					$this->nameRules[] = [
						'module' => $module($pathName),
						'file'   => $fileName,
						'path'   => $pathName,
					];
				}
				else {
					$basePolicy = str_replace('Policy', '', pathinfo($fileName)['filename']);
					$model      = poppy_path($module($pathName), 'src/models/' . $basePolicy . '.php');
					if (!app('files')->exists($model)) {
						$this->nameRules[] = [
							'module' => $module($pathName),
							'file'   => $fileName,
							'path'   => $pathName,
						];
					}
				}
			}
		};

		foreach ($iterator as $file) {
			$checkFile($file);
		}

		$this->warn('[Inspect:Name Rule]');
		if ($this->nameRules) {
			$this->table([
				'module' => 'Module', 'file' => 'FileName', 'path' => 'Path',
			], $this->nameRules);
		}
		else {
			$this->info('Beautiful, Name rules are matched.');
		}
	}

	private function inspectDbSeo()
	{
		$modules = app('poppy')->enabled();
		$models  = [];
		$modules->each(function ($item) use (&$models) {
			$slug  = $item['slug'];
			$path  = base_path('modules/' . $slug . '/src/models/*.php');
			$files = glob($path);
			$seoDb = [];
			foreach ($files as $file) {
				if (preg_match('/models\/(?<model>[A-Za-z]+)\.php/', $file, $matches)) {
					// dd($matches['model']);
					$key           = snake_case($matches['model']);
					$className     = poppy_class($slug, 'Models\\' . $matches['model']);
					$ref           = new ReflectionClass($className);
					$docComment    = $ref->getDocComment();
					$CommentParser = new CommentParser();
					$comments      = $CommentParser->parseMethod($docComment);
					$params        = $comments['params'] ?? [];
					$fields        = [];
					collect($params)->where('type', 'property')->each(function ($item) use (&$fields) {
						$desc  = $item['var_desc'] ?? '';
						$field = str_replace('$', '', $item['var_name']);
						if (preg_match('/(?<input_value>\[.*?\])/', $desc ?? '', $match)) {
							$desc = str_replace($match['input_value'], '', $desc);
						}
						$fields[$field] = $desc;
					});
					$seoDb[$key] = $fields;
				}
			}
			$models = array_merge($models, $seoDb);
		});
		sys_cache('system')->forever('system.lang.models', $models);
		$this->info('Cached models Success!');
	}

	/**
	 * 把所有的功能点都列出来
	 */
	private function inspectController()
	{
		$baseDir = base_path();
		$folders = glob($baseDir . '/{modules}/*/src', GLOB_BRACE);

		$table = [];
		foreach ($folders as $dir) {
			$files = app('files')->allFiles($dir);
			foreach ($files as $file) {
				$pathName   = $file->getPathname();
				$moduleName = $this->moduleName($pathName);

				// 排除指定的类
				if (!str_contains($pathName, ['http/request/'])) {
					continue;
				}

				// 模块名称解析错误
				if (!$moduleName) {
					$this->warn('Error module name in path:' . $pathName);

					return;
				}

				if (!preg_match('/request\/([a-z0-9_]+)\/([A-Z0-9a-z]+)Controller.php/', $pathName, $match)) {
					continue;
				}

				$requestGroup  = $match[1] ?? '';
				$requestAction = $match[2] ?? '';

				$relativePath = $file->getRelativePath();
				$className    = $this->className($moduleName, $relativePath, $file->getFilename());
				try {
					$refection = new ReflectionClass($className);
				} catch (Throwable $e) {
					$this->warn($moduleName . $e->getMessage());
					continue;
				}

				$methods = $refection->getMethods();
				if ($methods === null) {
					continue;
				}

				foreach ($methods as $method) {
					$methodName = $method->getName();
					// 排除继承的方法
					if ($method->class !== $className) {
						continue;
					}

					// 排除魔术方法
					if (starts_with($methodName, '__')) {
						continue;
					}

					// 不是本文件中的. 略过
					if (basename($method->getFileName()) !== basename($file->getRealPath())) {
						continue;
					}

					// 检查 注释
					$comment = $method->getDocComment();
					$item    = [
						$moduleName,
						$requestGroup,
						$requestAction,
						$methodName,
					];
					if (!$comment) {
						$item[] = '[comment: missing]';
					}
					else {
						$Parser = new CommentParser();
						$parsed = $Parser->parseMethod($comment);
						$item[] = str_replace(['/', PHP_EOL], '', $parsed['description'] ?? '');
					}

					$table[] = $item;
				}
			}
		}

		$this->table(['module', 'group', 'action', 'do', 'description'], $table);
	}

	/**
	 * 把所有的业务逻辑都列出来
	 */
	private function inspectAction()
	{
		$baseDir = base_path();
		$folders = glob($baseDir . '/{modules}/*/src/action', GLOB_BRACE);

		$table = [];
		foreach ($folders as $dir) {
			$files = app('files')->allFiles($dir);
			foreach ($files as $file) {
				$pathName   = $file->getPathname();
				$moduleName = $this->moduleName($pathName);

				// 排除指定的类
				if (!str_contains($pathName, ['action/'])) {
					continue;
				}

				// 模块名称解析错误
				if (!$moduleName) {
					$this->warn('Error module name in path:' . $pathName);

					return;
				}

				if (!preg_match('/action\/(\w+)\.php/', $pathName, $match)) {
					continue;
				}

				$action = $match[1] ?? '';

				$className = $this->className($moduleName, 'action', $file->getFilename());

				try {
					$refection = new ReflectionClass($className);
				} catch (Throwable $e) {
					$this->warn($moduleName . $e->getMessage());
					continue;
				}

				$methods = $refection->getMethods();
				if ($methods === null) {
					continue;
				}

				foreach ($methods as $method) {
					$methodName = $method->getName();
					// 排除继承的方法
					if (
						$method->class !== $className
						||
						$method->isPrivate()
						||
						$method->isProtected()
						||
						$method->isConstructor()
						||
						starts_with($methodName, ['set', 'get'])
					) {
						continue;
					}

					// 不是本文件中的. 略过
					if (basename($method->getFileName()) !== basename($file->getRealPath())) {
						continue;
					}

					// 检查 注释
					$comment = $method->getDocComment();
					$item    = [
						$moduleName,
						$action,
						$methodName,
					];
					if (!$comment) {
						$item[] = '[comment: missing]';
					}
					else {
						$Parser      = new CommentParser();
						$parsed      = $Parser->parseMethod($comment);
						$description = trim($parsed['description'] ?? '', "\/\n");

						$descriptions = explode(PHP_EOL, $description);
						$item[]       = str_replace(['/', PHP_EOL], '', $descriptions[0] ?? '');
					}

					$table[] = $item;
				}
			}
		}

		$this->table(['module', 'action', 'do', 'description'], $table);
	}

	/**
	 * 获取模块信息ß
	 * @param mixed $path path
	 * @return string
	 */
	private function moduleName($path): string
	{
		if (preg_match('/(.*)\/([a-z]{1,20})\/src/', $path, $match)) {
			return $match[2] ?? '';
		}

		return '';
	}

	/**
	 * 生成类名
	 * @param string $module        模块
	 * @param string $relative_path 相对路径
	 * @param string $file_name     文件名
	 * @return string
	 */
	private function className($module, $relative_path, $file_name): string
	{
		$className = ucfirst(camel_case($module));
		$paths     = explode('/', $relative_path);
		foreach ($paths as $path) {
			$className .= '\\' . ucfirst(camel_case($path));
		}
		$basename  = pathinfo($file_name);
		$className .= '\\' . $basename['filename'];

		return $className;
	}

	/**
	 * 是否驼峰类型
	 * @param string $str 字符串
	 * @return bool
	 */
	private function isCamelCase($str): bool
	{
		return camel_case($str) === $str;
	}
}