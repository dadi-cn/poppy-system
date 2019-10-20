<?php namespace Poppy\System\Module;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\System\Module\Repositories\Modules;
use Poppy\System\Module\Repositories\ModulesHook;
use Poppy\System\Module\Repositories\ModulesMenu;
use Poppy\System\Module\Repositories\ModulesPage;
use Poppy\System\Module\Repositories\ModulesService;
use Poppy\System\Module\Repositories\ModulesSetting;
use Poppy\System\Module\Repositories\ModulesUi;

/**
 * Class ModuleManager.
 */
class ModuleManager
{
	use PoppyTrait;

	/**
	 * @var Collection
	 */
	protected $excepts;

	/**
	 * @var ModulesMenu
	 */
	protected $menuRepository;

	/**
	 * @var ModulesPage
	 */
	protected $pageRepository;

	/**
	 * @var ModulesSetting
	 */
	protected $settingRepository;

	/**
	 * @var ModulesUi
	 */
	protected $uiRepository;

	/**
	 * @var ModulesHook
	 */
	protected $hooksRepo;

	/**
	 * @var ModulesService
	 */
	protected $serviceRepo;

	/**
	 * @var Modules
	 */
	protected $repository;

	/**
	 * ModuleManager constructor.
	 */
	public function __construct()
	{
		$this->excepts = collect();
	}

	/**
	 * @return Collection
	 */
	public function enabled()
	{
		return $this->repository()->enabled();
	}

	/**
	 * @return Modules
	 */
	public function repository(): Modules
	{
		if (!$this->repository instanceof Modules) {
			$this->repository = new Modules();
			$slugs            = app('poppy')->enabled()->pluck('slug');
			$this->repository->initialize($slugs);
		}
		return $this->repository;
	}

	/**
	 * Get a module by name.
	 * @param mixed $name name
	 * @return Module
	 */
	public function get($name): Module
	{
		return $this->repository()->get($name);
	}

	/**
	 * Check for module exist.
	 * @param mixed $name name
	 * @return bool
	 */
	public function has($name): bool
	{
		return $this->repository()->has($name);
	}

	/**
	 * @return array
	 */
	public function getExcepts(): array
	{
		return $this->excepts->toArray();
	}

	/**
	 * @return ModulesUi
	 */
	public function uis(): ModulesUi
	{
		if (!$this->uiRepository instanceof ModulesUi) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('ui', []));
			});
			$this->uiRepository = new ModulesUi();
			$this->uiRepository->initialize($collection);
		}

		return $this->uiRepository;
	}

	/**
	 * @return ModulesMenu
	 */
	public function menus(): ModulesMenu
	{
		if (!$this->menuRepository instanceof ModulesMenu) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('menus', []));
			});
			$this->menuRepository = new ModulesMenu();
			$this->menuRepository->initialize($collection);
		}

		return $this->menuRepository;
	}

	/**
	 * @return ModulesPage
	 */
	public function pages(): ModulesPage
	{
		if (!$this->pageRepository instanceof ModulesPage) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('pages', []));
			});
			$this->pageRepository = new ModulesPage();
			$this->pageRepository->initialize($collection);
		}

		return $this->pageRepository;
	}

	/**
	 * @return ModulesSetting
	 */
	public function settings(): ModulesSetting
	{
		if (!$this->settingRepository instanceof ModulesSetting) {
			$collection = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collection) {
				$collection->put($module->slug(), $module->get('settings', []));
			});
			$this->settingRepository = new ModulesSetting();
			$this->settingRepository->initialize($collection);
		}

		return $this->settingRepository;
	}

	/**
	 * @return ModulesHook
	 */
	public function hooks(): ModulesHook
	{
		if (!$this->hooksRepo instanceof ModulesHook) {
			$collect = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collect) {
				$collect->put($module->slug(), $module->get('hooks', []));
			});
			$this->hooksRepo = new ModulesHook();
			$this->hooksRepo->initialize($collect);
		}

		return $this->hooksRepo;
	}

	/**
	 * @return ModulesService(
	 */
	public function services(): ModulesService
	{
		if (!$this->serviceRepo instanceof ModulesService) {
			$collect = collect();
			$this->repository()->enabled()->each(function (Module $module) use ($collect) {
				$collect->put($module->slug(), $module->get('services', []));
			});
			$this->serviceRepo = new ModulesService();
			$this->serviceRepo->initialize($collect);
		}

		return $this->serviceRepo;
	}

	/**
	 * @param array $excepts 数据数组
	 */
	public function registerExcept($excepts): void
	{
		foreach ((array) $excepts as $except) {
			$this->excepts->push($except);
		}
	}
}
