<?php namespace Poppy\System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;
use Poppy\System\Models\SysConfig;

/**
 * 定义的钩子
 */
class ModulesHook extends Repository
{

	/**
	 * Initialize.
	 * @param Collection $data 集合
	 */
	public function initialize(Collection $data)
	{
		$this->items = sys_cache('system')->remember(
			'system.module.repo.hooks',
			SysConfig::MIN_HALF_DAY,
			function () use ($data) {
				$collection = collect();
				$data->each(function ($items) use ($collection) {
					$items = collect($items);
					$items->each(function ($item) use ($collection) {
						$service = app('module')->services()->get($item['name']);
						if ($service['type'] === 'array') {
							$data = (array) $collection->get($item['name']);
							$collection->put($item['name'], array_merge($data, $item['hooks']));
						}
						if ($service['type'] === 'form') {
							$collection->put($item['name'], $item['builder']);
						}
						if ($service['type'] === 'html') {
							$data = (array) $collection->get($item['name']);
							$collection->put($item['name'], array_merge($data, $item['hooks']));
						}
					});
				});

				return $collection->all();
			}
		);
	}
}
