<?php namespace Poppy\System\Models;

use Carbon\Carbon;
use Eloquent;

/**
 * 验证码
 * @property int    $id          id
 * @property string $type        验证类型
 * @property int    $num         发送次数
 * @property string $passport    手机号或邮箱
 * @property string $captcha     验证码
 * @property Carbon $disabled_at 失效时间
 * @property Carbon $created_at  创建时间
 * @property Carbon $updated_at  修改时间
 */
class SysCaptcha extends Eloquent
{
	public const TYPE_MOBILE = 'mobile';
	public const TYPE_MAIL   = 'mail';

	public const CON_LOGIN       = 'login';
	public const CON_REGISTER    = 'register';
	public const CON_PASSWORD    = 'password';
	public const CON_USER        = 'user';
	public const CON_REBIND      = 'rebind';
	public const CON_BIND_NOTICE = 'bind_notice';

	protected $table = 'sys_captcha';

	protected $dates = [
		'disabled_at',
	];

	protected $fillable = [
		'type',
		'num',
		'passport',
		'captcha',
		'disabled_at',
	];

	/**
	 * 操作说明
	 * @param null|string $key          Key
	 * @param bool        $check_exists 检测键值是否存在
	 * @return array|string
	 */
	public static function kvTypeDesc($key = null, $check_exists = false)
	{
		$desc = [
			self::CON_LOGIN    => '登录',
			self::CON_PASSWORD => '重置密码',
			self::CON_USER     => '验证身份',
			self::CON_REBIND   => '重新绑定',
		];

		return kv($desc, $key, $check_exists);
	}
}