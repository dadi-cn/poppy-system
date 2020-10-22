<?php namespace Poppy\System\Http\Request\Develop;

use Auth;
use Faker\Generator;
use Illuminate\Contracts\Auth\Authenticatable;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Poppy\System\Models\PamAccount;
use View;

/**
 * 开发平台初始化
 */
class DevelopController extends Controller
{
	use ViewTrait, CoreTrait;

	/**
	 * Faker
	 * @var Generator
	 */
	protected $faker;

	public function __construct()
	{
		parent::__construct();
		View::share('_menus', $this->coreModule()->menus()->where('type', 'develop')->toArray());
		$this->faker = app(Generator::class);
	}

	/**
	 * 返回用户信息
	 * @return Authenticatable|null|PamAccount
	 */
	protected function pam()
	{
		return Auth::guard(PamAccount::GUARD_DEVELOP)->user();
	}

	/**
	 * 假的图片地址
	 */
	protected function fakeImage()
	{
		$images = config('module.system.develop.fake_images');
		if (!$images) {
			return url('resources/assets/images/logo.png');
		}
		return collect($images)->shuffle()->first();
	}
}
