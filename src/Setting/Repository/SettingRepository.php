<?php namespace Poppy\System\Setting\Repository;

use DB;
use Exception;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\System\Models\SysConfig;
use Poppy\System\Setting\Contracts\SettingContract;

/**
 * system config
 * Setting Repository
 */
class SettingRepository implements SettingContract
{
	use KeyParserTrait, AppTrait, CoreTrait;

	/**
	 * @var bool 检查是否存在这个数据表
	 */
	private $hasTable = false;

	/**
	 * @var bool 是否重新读取
	 */
	private $reRead = false;

	/**
	 * @var array 查询缓存
	 */
	private static $cache;

	public function __construct()
	{
		$tableName = (new SysConfig())->getTable();

		static::$cache = (array) sys_cache('system')->get('system.setting.repo');
		if (static::$cache) {
			$this->hasTable = true;
		}
		else {
			$hasDb   = sys_container()->hasDatabase();
			$builder = DB::getSchemaBuilder();
			if ($hasDb && $builder->hasTable($tableName)) {
				$this->hasTable = true;
			}
		}
	}

	/**
	 * Resets a setting value by deleting the record.
	 * @param string $key specifies the setting key value
	 * @return bool
	 * @throws Exception
	 */
	public function delete($key): bool
	{
		if (!$this->keyParserMatch($key)) {
			return $this->setError(trans('system::util.setting.key_not_match', [
				'key' => $key,
			]));
		}
		$record = $this->findRecord($key);
		if (!$record) {
			return false;
		}

		$record->delete();

		unset(static::$cache[$key]);

		return true;
	}

	/**
	 * Returns a setting value by the module (or plugin) name and setting name.
	 * @param string $key     Specifies the setting key value, for example 'system:updates.check'
	 * @param mixed  $default the default value to return if the setting doesn't exist in the DB
	 * @return mixed returns the setting value loaded from the database or the default value
	 */
	public function get($key, $default = '')
	{
		if ($this->reRead) {
			static::$cache = (array) sys_cache('system')->get('system.setting.repo');
		}

		if (array_key_exists($key, static::$cache)) {
			return static::$cache[$key];
		}

		if (!$this->keyParserMatch($key)) {
			return $this->setError(trans('system::util.setting.key_not_match', [
				'key' => $key,
			]));
		}

		if (!$this->hasTable) {
			return '';
		}
		$record = $this->findRecord($key);
		if (!$record) {
			// get default by setting.yaml
			$settingItem = $this->coreModule()->settings()->get($key);
			if ($settingItem) {
				$type           = $settingItem['type'] ?? 'string';
				$defaultSetting = $settingItem['default'] ?? '';
				switch ($type) {
					case 'string':
					default:
						$default = $defaultSetting;
						break;
					case 'int':
						$default = (int) $defaultSetting;
						break;
					case 'bool':
					case 'boolean':
						$default = (bool) $defaultSetting;
						break;
				}
			}

			static::$cache[$key] = $default;

			return static::$cache[$key];
		}

		static::$cache[$key] = unserialize($record->value);

		$this->save();

		return static::$cache[$key];
	}

	/**
	 * Stores a setting value to the database.
	 * @param mixed $key   Specifies the setting key value, for example 'system:updates.check'
	 * @param mixed $value the setting value to store, serializable
	 * @return bool
	 */
	public function set($key, $value = ''): bool
	{
		if (is_array($key)) {
			foreach ($key as $_key => $_value) {
				$this->set($_key, $_value);
			}

			return true;
		}

		if (!$this->keyParserMatch($key)) {
			return $this->setError(trans('system::util.setting.key_not_match', [
				'key' => $key,
			]));
		}

		$record = $this->findRecord($key);
		if (!$record) {
			$record                     = new SysConfig();
			[$namespace, $group, $item] = $this->parseKey($key);
			$record->namespace          = $namespace;
			$record->group              = $group;
			$record->item               = $item;
		}
		$record->value = serialize($value);
		$record->save();
		static::$cache[$key] = $value;
		// 写入缓存
		$this->save();

		return true;
	}

	/**
	 * 根据命名空间从数据库中获取数据
	 * @param string $namespace_with_group 命名空间和分组
	 * @return array
	 */
	public function getNG($namespace_with_group): array
	{
		[$ns, $group] = explode('::', $namespace_with_group);
		if (!$ns || !$group) {
			return [];
		}
		$values = SysConfig::where('namespace', $ns)->where('group', $group)->select(['item', 'value'])->get();
		$data   = collect();
		$values->each(function ($item) use ($data) {
			$data->put($item['item'], unserialize($item['value']));
		});

		return $data->toArray();
	}

	/**
	 * Returns a record (cached)
	 * @param string $key 获取的key
	 * @return SysConfig|null
	 */
	private function findRecord($key)
	{
		/** @var SysConfig $record */
		$record = SysConfig::query();

		return $record->applyKey($key)->first();
	}

	/**
	 * 保存配置
	 */
	public function save(): void
	{
		sys_cache('system')->forever('system.setting.repo', static::$cache);
	}

	/**
	 * 设置是否重新读取缓存
	 * @param bool $reRead 标识
	 */
	public function setReRead(bool $reRead): void
	{
		$this->reRead = $reRead;
	}
}
