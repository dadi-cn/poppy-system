<?php namespace Poppy\System\Classes\Loader;

use Poppy\Framework\Foundation\Application;
use Poppy\Framework\Helper\StrHelper;
use Throwable;

/**
 * 扩展加载器
 */
class AddonLoader
{

	/**
	 * @var string 路径
	 */
	private $manifestPath;

	/**
	 * @var bool 是否需要提花
	 */
	private $manifestDirty;

	/**
	 * @var array 维护数据
	 */
	private $manifest;

	/**
	 * @var bool 是否已经注册
	 */
	private $registered;

	/**
	 * AddonLoader constructor.
	 * @param \Illuminate\Contracts\Foundation\Application|Application $app
	 */
	public function __construct($app)
	{
		$this->manifestPath = $app->getCachedClassesPath();
	}

	/**
	 * 检测给定相对路径是否是存在的文件
	 * @param string $path 路径信息
	 * @return bool
	 */
	protected function isRealFilePath($path): bool
	{
		return is_file(realpath(base_path($path)));
	}

	/**
	 * 注册加载器
	 * @return void
	 */
	public function register()
	{
		if ($this->registered) {
			return;
		}

		$this->ensureManifestIsLoaded();

		$this->registered = spl_autoload_register([$this, 'load']);
	}

	/**
	 * 确保清单已经加载进内存
	 */
	protected function ensureManifestIsLoaded()
	{
		if (!is_null($this->manifest)) {
			return;
		}

		if (file_exists($this->manifestPath)) {
			try {
				$this->manifest = app('files')->getRequire($this->manifestPath);

				if (!is_array($this->manifest)) {
					$this->manifest = [];
				}
			} catch (Throwable $ex) {
				$this->manifest = [];
			}
		}
		else {
			$this->manifest = [];
		}
	}

	/**
	 * 包含一个类并且添加到 manifest 中
	 * @param string $class 需要加载的类
	 * @param string $path  路径
	 * @return void
	 */
	protected function includeClass($class, $path)
	{
		require_once base_path($path);

		$this->manifest[$class] = $path;

		$this->manifestDirty = true;

		$this->build();
	}

	/**
	 * 加载指定的名称
	 * @param string $name 指定的名称
	 * @return bool
	 */
	public function load($name)
	{
		$className = StrHelper::normalizeClassName($name);
		if (
			isset($this->manifest[$className]) &&
			$this->isRealFilePath($path = $this->manifest[$className])
		) {
			require_once base_path($path);

			return true;
		}

		if (starts_with($className, '\Addons')) {
			$names = explode('\\', $className);

			array_shift($names);
			array_shift($names);
			$group  = array_shift($names);
			$plugin = array_shift($names);
			$file   = array_pop($names);
			$path   = 'addons/' . snake_case($group) . '/' . snake_case($plugin) . '/src/' .
				($names ? snake_case(implode('/', $names)) . '/' : '') . $file . '.php';

			if ($this->isRealFilePath($path)) {
				$this->includeClass($className, $path);

				return true;
			}
		}

		return false;
	}

	/**
	 * 写入维护数据
	 */
	private function build()
	{
		if (!$this->manifestDirty) {
			return;
		}
		$this->write($this->manifest);
	}

	/**
	 * 清单写入在磁盘
	 * @param array $manifest 数据
	 */
	private function write(array $manifest)
	{
		app('files')->put(
			$this->manifestPath,
			'<?php return ' . var_export($manifest, true) . ';'
		);
	}
}

