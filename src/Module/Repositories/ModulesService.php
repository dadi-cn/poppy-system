<?php namespace Poppy\System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Support\Abstracts\Repository;
use Poppy\System\Models\SysConfig;

/**
 * 定义的服务项
 */
class ModulesService extends Repository
{
	use PoppyTrait;

	/**
	 * Initialize.
	 * @param Collection $data 集合
	 */
	public function initialize(Collection $data)
	{
		$this->items = sys_cache('system')->remember(
			'system.module.repo.service',
			SysConfig::MIN_HALF_DAY,
			function () use ($data) {
				$collection = collect();
				$data->each(function ($items) use ($collection) {
					$items = collect($items);
					$items->each(function ($item, $key) use ($collection) {
						$collection->put($key, $item);
					});
				});

				return $collection->all();
			}
		);
	}
}
