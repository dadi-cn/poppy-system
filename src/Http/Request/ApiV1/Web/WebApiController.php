<?php namespace Poppy\System\Http\Request\ApiV1\Web;

use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Poppy\Framework\Application\ApiController;
use Poppy\System\Models\PamAccount;

/**
 * Web api 控制器
 */
class WebApiController extends ApiController
{
	private $pam;

	/**
	 * 返回 Jwt 用户
	 * @return Authenticatable|PamAccount
	 */
	protected function jwtPam()
	{
		if ($this->pam){
			return $this->pam;
		}

		return Auth::guard(PamAccount::GUARD_JWT_WEB)->user();
	}

	/**
	 * @param $pam
	 * @return $this
	 */
	public function setPam($pam)
	{
		$this->pam = $pam;

		return $this;
	}
}