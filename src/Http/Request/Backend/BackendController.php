<?php namespace Poppy\System\Http\Request\Backend;

use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Poppy\Framework\Application\Controller;
use Poppy\System\Models\PamAccount;

/**
 * 后台初始化控制器
 */
class BackendController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		sys_container()->setExecutionContext('backend');
	}

	/**
	 * 当前用户
	 * @return Authenticatable|PamAccount
	 */
	public function pam()
	{
		return Auth::guard(PamAccount::GUARD_BACKEND)->user();
	}
}