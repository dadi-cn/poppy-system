<?php namespace Poppy\System\Classes\Auth\Password;

use Poppy\System\Classes\Contracts\Auth\Password;
use Poppy\System\Models\PamAccount;

/**
 * 后台用户认证
 */
class DefaultPasswordProvider implements Password
{
	/**
	 * @param PamAccount $pam      账号
	 * @param string     $password 密码
	 * @param string     $type     密码类型
	 * @return bool|mixed
	 */
	public function check($pam, $password, $type = 'plain')
	{
		return $this->genPassword($password, $pam->created_at, $pam->password_key) === $pam->password;
	}


	/**
	 * 生成密码
	 * @param string $password     密码
	 * @param string $reg_datetime 注册日期
	 * @param string $account_key  账号KEY
	 * @return mixed
	 */
	public function genPassword($password, $reg_datetime, $account_key)
	{
		return md5(sha1($password . $reg_datetime) . $account_key);
	}
}