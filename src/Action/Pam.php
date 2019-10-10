<?php namespace Poppy\System\Action;

use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Runner\Exception;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Classes\Auth\Password\DefaultPasswordProvider;
use Poppy\System\Classes\Contracts\Auth\Password;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Classes\Traits\UserSettingTrait;
use Poppy\System\Events\LoginFailedEvent;
use Poppy\System\Events\LoginSuccessEvent;
use Poppy\System\Events\PamDisableEvent;
use Poppy\System\Events\PamEnableEvent;
use Poppy\System\Events\PamRegisteredEvent;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\SysCaptcha;
use Poppy\System\Models\SysConfig;
use Throwable;
use Tymon\JWTAuth\JWTGuard;
use User\Models\UserManagerLog;
use Validator;

/**
 * 账号操作
 */
class Pam
{
	use UserSettingTrait, AppTrait, PamTrait;

	/**
	 * @var int 父级ID
	 */
	private $parentId = 0;

	/**
	 * @var string Pam table
	 */
	private $pamTable;

	public function __construct()
	{
		$this->pamTable = (new PamAccount())->getTable();
	}

	/**
	 * 验证验登录
	 * @param string $passport 通行证
	 * @param string $captcha  验证码
	 * @param string $platform 平台
	 * @return bool
	 */
	public function captchaLogin($passport, $captcha, $platform): bool
	{
		$initDb = [
			'passport' => $passport,
			'captcha'  => $captcha,
		];

		// 数据验证
		$validator = Validator::make($initDb, [
			'captcha'  => Rule::required(),
			'platform' => Rule::in(PamAccount::kvPlatform()),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// 验证账号 + 验证码
		$verification = new Verification();

		if (!$verification->check($passport, $captcha)) {
			return $this->setError($verification->getError()->getMessage());
		}
		$verification->delete($passport);

		// 判定账号是否存在, 如果不存在则进行注册
		$this->pam = PamAccount::passport($passport);
		if (!$this->pam && !$this->register($initDb['passport'], '', PamRole::FE_USER, $platform)) {
			return false;
		}

		// 检测权限, 是否被禁用
		if (!$this->checkPamPermission($this->pam)) {
			return false;
		}

		event(new LoginSuccessEvent($this->pam, $platform));

		return true;
	}

	/**
	 * 设置父级ID
	 * @param int $parent_id 父级id
	 */
	public function setParentId($parent_id): void
	{
		$this->parentId = $parent_id;
	}

	/**
	 * 用户注册
	 * @param string $passport  passport
	 * @param string $password  密码
	 * @param string $role_name 用户角色名称
	 * @param string $platform  支持的平台
	 * @return bool
	 */
	public function register($passport, $password = '', $role_name = PamRole::FE_USER, $platform = ''): bool
	{
		// 组织数据 -> 根据数据库字段来组织
		$passport = strtolower($passport);

		$type = $this->passportType($passport);

		$initDb = [
			$type          => (string) $passport,
			'password'     => (string) $password,
			'reg_platform' => (string) $platform,
			'parent_id'    => $this->parentId,
		];

		$rule = [
			$type      => [
				Rule::required(),
				Rule::string(),
				Rule::between(6, 30),
				// 唯一性认证
				Rule::unique($this->pamTable, $type),
			],
			'password' => [
				Rule::string(),
			],
		];

		// 完善主账号类型规则
		if ($type === PamAccount::REG_TYPE_MOBILE) {
			$rule[$type][] = Rule::mobile();
		}
		elseif ($type === PamAccount::REG_TYPE_EMAIL) {
			$rule[$type][] = Rule::email();
		}
		else {
			if (preg_match('/\s+/', $passport)) {
				return $this->setError(trans('system::action.pam.user_name_not_space'));
			}
			// 注册用户时候的正则匹配
			if ($this->parentId) {
				// 子用户中必须包含 ':' 冒号
				if (strpos($initDb[$type], ':') === false) {
					return $this->setError(trans('system::action.pam.sub_user_account_need_colon'));
				}
				// 初始化子用户数据
				$initDb['parent_id'] = $this->parentId;

				// 注册子用户
				array_unshift($rule[$type], 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][:a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u');
			}
			else {
				array_unshift($rule[$type], 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u');
			}
		}

		// 密码不为空时候的检测
		if ($password !== '') {
			$rule['password'] += [
				Rule::between(6, 16),
				Rule::required(),
				Rule::password(),
			];
		}

		// 验证数据
		$validator = Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$roleNames = (array) $role_name;
		/** @var PamRole $role */
		$role = PamRole::whereIn('name', $roleNames)->get();
		if (!$role) {
			return $this->setError(trans('system::action.pam.role_not_exists'));
		}

		// 自动设置前缀
		$prefix = strtoupper(strtolower(sys_setting('system::pam.prefix')));
		if ($type !== PamAccount::REG_TYPE_USERNAME) {
			$hasAccountName = false;
			// 检查是否设置了前缀
			if (!$prefix) {
				return $this->setError(trans('system::action.pam.not_set_name_prefix'));
			}
			$username = $prefix . '_' . Carbon::now()->format('YmdHis') . str_random(6);
		}
		else {
			$hasAccountName = true;
			$username       = $passport;
		}

		$initDb['username']  = $username;
		$initDb['type']      = $role->first()->type;
		$initDb['is_enable'] = SysConfig::ENABLE;

		try {
			// 处理数据库
			return DB::transaction(function () use ($initDb, $role, $password, $hasAccountName, $prefix) {
				/** @var PamAccount $pam pam */
				$pam = PamAccount::create($initDb);

				// 给用户默认角色
				$pam->roles()->attach($role->pluck('id'));

				// 如果没有设置账号, 则根据规范生成用户名
				if (!$hasAccountName) {
					$formatAccountName = sprintf("%s_%'.09d", $prefix, $pam->id);
					$pam->username     = $formatAccountName;
					$pam->save();
				}

				if ($password) {
					// 设置密码
					$this->setPassword($pam, $password);
				}

				// 触发注册成功的事件
				event(new PamRegisteredEvent($pam));

				$this->pam = $pam;

				return true;
			});
		} catch (Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 密码登录
	 * @param string $passport   passport
	 * @param string $password   密码
	 * @param string $guard_type 类型
	 * @param string $platform   平台
	 * @return bool
	 */
	public function loginCheck($passport, $password, $guard_type = PamAccount::GUARD_WEB, $platform = ''): bool
	{
		$pamTable = (new PamAccount())->getTable();

		$type        = $this->passportType($passport);
		$credentials = [
			$type      => $passport,
			'password' => $password,
		];

		// check exists
		$validator = Validator::make($credentials, [
			$type      => [
				Rule::required(),
				Rule::exists($pamTable, $type),
			],
			'password' => Rule::required(),
		], []);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		$guard = Auth::guard($guard_type);

		if ($guard->attempt($credentials)) {
			// jwt 不能获取到 user， 使用 getLastAttempted 方法来获取数据
			if ($guard instanceof JWTGuard) {
				/** @var PamAccount $pam */
				$pam = $guard->getLastAttempted();
			}
			else {
				/** @var PamAccount $user */
				$pam = $guard->user();
			}
			$this->pam = $pam;

			if (!$this->checkPamPermission($this->pam)) {
				return false;
			}

			if (method_exists($this, 'loginAllowIpCheck') && !$this->loginAllowIpCheck()) {
				$guard->logout();
				return false;
			}

			event(new LoginSuccessEvent($pam, $platform));

			return true;
		}
		$credentials += [
			'type'     => $type,
			'passport' => $passport,
		];

		event(new LoginFailedEvent($credentials));

		return $this->setError(trans('system::action.pam.login_fail_again'));
	}

	/**
	 * 找回手机号
	 * @param string $passport passport
	 * @return bool
	 */
	public function findMobile($passport)
	{
		$type   = $this->passportType($passport);
		$initDb = [
			$type => (string) $passport,
		];
		$rule   = [
			Rule::required(),
			Rule::exists($this->pamTable, $type),
			Rule::string(),
		];

		// 完善主账号类型规则
		if ($type === PamAccount::REG_TYPE_MOBILE) {
			$rule[$type][] = Rule::mobile();
		}
		elseif ($type === PamAccount::REG_TYPE_EMAIL) {
			$rule[$type][] = Rule::email();
		}
		else {
			if (preg_match('/\s+/', $passport)) {
				return $this->setError(trans('system::action.pam.account_disable_not_login'));
			}
			$rule[$type][] = 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u';
		}

		$validator = Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
	}

	/**
	 * 设置登录密码
	 * @param PamAccount $pam      用户
	 * @param string     $password 密码
	 * @return bool
	 */
	public function setPassword($pam, $password): bool
	{
		if (!$pam && !($pam instanceof PamAccount)) {
			return $this->setError(trans('system::action.pam.pam_error'));
		}
		$validator = Validator::make([
			'password' => $password,
		], [
			'password' => 'required|between:6,20',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$key               = str_random(6);
		$regDatetime       = $pam->created_at->toDateTimeString();
		$cryptPassword     = $this->password()->genPassword($password, $regDatetime, $key);
		$pam->password     = $cryptPassword;
		$pam->password_key = $key;
		$pam->save();

		return true;
	}

	/**
	 * 设置角色
	 * @param PamAccount $pam   账号数据
	 * @param array      $roles 角色名
	 * @return bool
	 */
	public function setRoles($pam, $roles): bool
	{
		/** @var PamRole[]|Collection $role */
		$role = PamRole::whereIn('name', $roles)->get();
		$pam->roles()->detach();
		$pam->roles()->attach($role->pluck('id'));

		return true;
	}

	/**
	 * 新手机号发送验证码
	 * @param string $verify_code 验证码
	 * @param string $newMobile   手机号
	 * @return bool
	 */
	public function newSendCaptcha($verify_code, $newMobile)
	{
		$data      = [
			'verify_code' => $verify_code,
			'mobile'      => $newMobile,
		];
		$validator = Validator::make($data, [
			'verify_code' => [
				Rule::required(),
				Rule::string(),
			],
			'mobile'      => [
				Rule::required(),
				Rule::mobile(),
			], [], [
				'verify_code' => '验证串码',
				'mobile'      => '手机号',
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		$actUtil = new Verification();
		if (!$actUtil->verifyOnceCode($verify_code)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		//验证新手机号是否已经注册
		if (PamAccount::where('mobile', $newMobile)->exists()) {
			return $this->setError(trans('system::action.pam.mobile_already_registered'));
		}

		//发送验证码
		$actUtil->send($newMobile, $type = SysCaptcha::CON_LOGIN);

		return true;
	}

	/**
	 * 新的手机号验证
	 * @param string $verify_code 验证码
	 * @param string $passport    passport
	 * @param string $captcha     验证码
	 * @return bool
	 */
	public function newPassport($verify_code, $passport, $captcha)
	{
		if (!$this->checkPam()) {
			return false;
		}

		$data      = [
			'verify_code' => $verify_code,
			'mobile'      => $passport,
			'captcha'     => $captcha,
		];
		$validator = Validator::make($data, [
			'verify_code' => [
				Rule::required(),
				Rule::string(),
			],
			'mobile'      => [
				Rule::required(),
				Rule::mobile(),
			],
			'captcha'     => [
				Rule::required(),
			], [], [
				'verify_code' => '验证串码',
				'mobile'      => '手机号',
				'captcha'     => '验证码',
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$Captcha = new Verification();
		// 验证验证码
		if (!$Captcha->check($passport, $captcha)) {
			return $this->setError($Captcha->getError()->getMessage());
		}
		$Captcha->delete($passport);

		// 验证一次码
		if (!$Captcha->verifyOnceCode($verify_code)) {
			return $this->setError($Captcha->getError()->getMessage());
		}

		try {
			$this->pam->update([
				'mobile' => $passport,
			]);

			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 生成支持 passport 格式的数组
	 * @param array|Request $credentials 待转化的数据
	 * @return array
	 */
	public function passportData($credentials): array
	{
		if ($credentials instanceof Request) {
			$credentials = $credentials->all();
		}
		$passport     = $credentials['passport'] ?? '';
		$passport     = $passport ?: $credentials['mobile'] ?? '';
		$passport     = $passport ?: $credentials['username'] ?? '';
		$passport     = $passport ?: $credentials['email'] ?? '';
		$passportType = $this->passportType($passport);

		return [
			$passportType => $passport,
			'password'    => $credentials['password'] ?? '',
		];
	}

	/**
	 * Passport Type
	 * @param string $passport 通行证
	 * @return string
	 */
	public function passportType($passport): string
	{
		if (UtilHelper::isMobile($passport)) {
			$type = PamAccount::REG_TYPE_MOBILE;
		}
		elseif (UtilHelper::isEmail($passport)) {
			$type = PamAccount::REG_TYPE_EMAIL;
		}
		elseif (is_numeric($passport)) {
			$type = 'id';
		}
		else {
			$type = PamAccount::REG_TYPE_USERNAME;
		}

		return $type;
	}

	/**
	 * 修改账户密码
	 * @param int    $id       用户id
	 * @param string $password 密码
	 * @return bool
	 */
	public function setPasswordById($id, $password): bool
	{
		if (!PamAccount::where('id', $id)->exists()) {
			return $this->setError(trans('system::action.pam.account_not_exist'));
		}
		$pam = PamAccount::find($id);

		if (!$this->setPassword($pam, $password)) {
			return false;
		}

		return true;
	}

	/**
	 * 后台用户禁用
	 * @param int    $id       用户id
	 * @param string $to       解禁时间
	 * @param string $reason   禁用原因
	 * @param array  $pictures 图片
	 * @return bool
	 */
	public function disable($id, $to, $reason, $pictures = []): bool
	{
		$data      = [
			'disable_reason' => (string) $reason,
			'disable_to'     => (string) $to,
		];
		$validator = Validator::make($data, [
			'disable_reason' => [
				Rule::string(),
			],
			'disable_to'     => [
				Rule::string(),
				Rule::dateFormat('Y-m-d'),
			], [], [
				'disable_reason' => trans('system::action.pam.disable_reason'),
				'disable_to'     => trans('system::action.pam.disable_to'),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		/** @var PamAccount $pam */
		$pam = PamAccount::find($id);
		//当前用户已禁用
		// if (!$pam->is_enable) {
		// 	return $this->setError(trans('system::action.pam.account_disabled'));
		// }
		/* @version 2.7 可以多次禁用
		 * ---------------------------------------- */

		$disable_end_at = Carbon::createFromFormat('Y-m-d', $data['disable_to'])->startOfDay();
		$pam->update([
			'is_enable'        => SysConfig::DISABLE,
			'disable_reason'   => $data['disable_reason'],
			'disable_start_at' => Carbon::now(),
			'disable_end_at'   => $disable_end_at,
		]);

		event(new PamDisableEvent($pam, [
			'editor_pam' => $this->pam,
			'type'       => UserManagerLog::TYPE_PROHIBIT,
			'log'        => UserManagerLog::kvType(UserManagerLog::TYPE_PROHIBIT) . $disable_end_at,
			'reason'     => $reason,
			'pictures'   => $pictures,
		]));

		return true;
	}

	/**
	 * 后台用户启用
	 * @param int    $id       用户Id
	 * @param string $reason   原因
	 * @param array  $pictures 图片
	 * @return bool
	 */
	public function enable($id, $reason = '', $pictures = []): bool
	{
		if (PamAccount::where('id', $id)->where('is_enable', 1)->exists()) {
			return $this->setError(trans('system::action.pam.account_enabled'));
		}
		try {
			PamAccount::where('id', $id)->update([
				'is_enable' => SysConfig::ENABLE,
			]);

			event(new PamEnableEvent(PamAccount::find($id), [
				'editor_pam' => $this->pam,
				'type'       => UserManagerLog::TYPE_CANCEL_PROHIBIT,
				'log'        => UserManagerLog::kvType(UserManagerLog::TYPE_CANCEL_PROHIBIT),
				'reason'     => $reason,
				'pictures'   => $pictures,
			]));

			return true;
		} catch (Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 自动解禁
	 */
	public function autoEnable(): bool
	{
		try {
			PamAccount::where('disable_end_at', '<', Carbon::now())->update([
				'is_enable' => SysConfig::ENABLE,
			]);

			return true;
		} catch (Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 清除登录日志
	 * @return bool
	 */
	public function clearLog(): bool
	{
		try {
			// 删除 60 天以外的登录日志
			PamLog::where('created_at', '<', Carbon::now()->subDays(60))->delete();

			return true;
		} catch (Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 验证用户权限
	 * @param PamAccount $pam 用户
	 * @return bool
	 */
	private function checkPamPermission($pam): bool
	{
		if ($pam->is_enable === SysConfig::NO) {
			// 账户被禁用
			$reason = sys_setting('system::pam.disabled_reason') ?: trans('system::action.pam.account_disable_not_login');

			return $this->setError($reason);
		}

		return true;
	}


	/**
	 * @return Password 密码对象
	 */
	public function password(): Password
	{
		$pwdClass = config('module.system.password_checker') ?: DefaultPasswordProvider::class;
		/** @var Password $Password */
		return new $pwdClass();
	}

	/**
	 * 修改密码
	 * @param string $old_password 老密码
	 * @param string $password     新密码
	 * @return bool
	 */
	public function changePassword($old_password, $password): bool
	{
		if (!$this->checkPam()) {
			return false;
		}
		$old_password = trim($old_password);
		$password     = trim($password);

		if ($old_password === $password) {
			return $this->setError('新旧密码不能相同');
		}

		if (!$this->password()->check($this->pam, $old_password)) {
			return $this->setError('旧密码不正确');
		}

		return $this->setPassword($this->pam, $password);
	}
}
