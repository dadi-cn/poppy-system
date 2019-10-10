<?php namespace Poppy\System\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\System\Action\Sms;

/**
 * 系统设置
 * @property int    $id          配置id
 * @property string $namespace   命名空间
 * @property string $group       配置分组
 * @property string $item        配置名称
 * @property string $value       配置值
 * @property string $description 配置介绍
 * @mixin Eloquent
 * @method static Builder|SysConfig applyKey($key)
 */
class SysConfig extends Eloquent
{
	use KeyParserTrait;

	// 数据库使用 0/1 来代表关/开
	public const YES = 1;
	public const NO  = 0;

	// 启用停用标识使用 enable/disable 来进行标识
	public const ENABLE  = 1;
	public const DISABLE = 0;

	// time define
	public const MIN_DEBUG     = 0;
	public const MIN_ONE_HOUR  = 60;
	public const MIN_SIX_HOUR  = 360;
	public const MIN_HALF_DAY  = 720;
	public const MIN_ONE_DAY   = 1440;
	public const MIN_HALF_WEEK = 5040;
	public const MIN_ONE_WEEK  = 10080;
	public const MIN_ONE_MONTH = 43200;

	protected $table = 'sys_config';

	protected $fillable = [
		'namespace',
		'group',
		'item',
		'value',
		'description',
	];

	public $timestamps = false;

	/**
	 * @param null $key key
	 * @return array|string
	 */
	public static function kvYn($key = null)
	{
		$desc = [
			self::NO  => '否',
			self::YES => '是',
		];

		return kv($desc, $key);
	}

	/**
	 * Scope to find a setting record for the specified module (or plugin) name and setting name.
	 * @param Builder $query query
	 * @param string  $key   Specifies the setting key value, for example 'system:updates.check'
	 * @return Builder
	 */
	public function scopeApplyKey($query, $key)
	{
		[$namespace, $group, $item] = $this->parseKey($key);

		$query = $query
			->where('namespace', $namespace)
			->where('group', $group)
			->where('item', $item);

		return $query;
	}

	/*
	|--------------------------------------------------------------------------
	| 短信模板
	|--------------------------------------------------------------------------
	|
	*/

	/**
	 * 短信类型
	 * @param string|null $key       key
	 * @param bool        $check_key 检测key是否存在
	 * @return array|string
	 */
	public static function kvSmsType($key = null, $check_key = false)
	{
		$desc = config('module.system.sms_type') ?: [];
		return kv($desc, $key, $check_key);
	}

	/**
	 * 获取指定平台对应类型的模板
	 * @param string $type 类型
	 * @return array|null [type|code|content|id]
	 */
	public static function smsTpl($type): ?array
	{
		$template = collect((new Sms())->getTemplates())->where('type', $type)->first();

		return $template ?? null;
	}
}