<?php

namespace Poppy\System\Listeners\AuthFailed;

use Poppy\Framework\Helper\EnvHelper;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;

/**
 * 失败日志
 */
class LogListener
{
	/**
	 * Handle the event.
	 * @param array $credentials 数据数组
	 * @return void
	 */
	public function handle($credentials)
	{
		// todo 这里需要加上后台的开关, 来进行错误日志的收集
		return;
		$account      = PamAccount::getByAccountName($credentials['account_name']);
		$account_id   = '';
		$account_name = $credentials['account_name'];
		$account_type = '';
		if ($account) {
			$account_id   = $account['account_id'];
			$account_type = $account['account_type'];
		}
		$content = '尝试登陆失败, 用户信息不匹配';
		if ($account_type != $credentials['account_type']) {
			$content = '范围[' . $account_type . ']用户跨域登陆, 登录失败';
		}
		PamLog::create([
			'account_id'   => $account_id,
			'account_name' => $account_name,
			'account_type' => $account_type,
			'log_type'     => 'error',
			'log_ip'       => EnvHelper::ip(),
			'log_content'  => $content,
		]);
	}
}

