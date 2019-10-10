<?php namespace Poppy\System\Classes\Contracts\Auth;

use Poppy\System\Models\PamAccount;

interface Password
{
	/**
	 * @param PamAccount $pam      账号信息
	 * @param string     $password 密码
	 * @param string     $type     认证类型 [plain|明文;]
	 * @return mixed
	 */
	public function check($pam, $password, $type = 'plain');


	/**
	 * 生成密码
	 * @param string $password     密码
	 * @param string $reg_datetime 注册日期
	 * @param string $account_key  账号KEY
	 * @return mixed
	 */
	public function genPassword($password, $reg_datetime, $account_key);
}