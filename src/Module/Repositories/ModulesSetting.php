<?php namespace Poppy\System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Support\Abstracts\Repository;

/**
 * Class AssetsRepository.
 */
class ModulesSetting extends Repository
{
	use PoppyTrait;

	/**
	 * Initialize.
	 * @param Collection $data 集合
	 */
	public function initialize(Collection $data)
	{
		$this->items = sys_cache('system')->rememberForever(
			'system.module.repo.setting',
			function () use ($data) {
				$collection = collect();
				$data->each(function ($items, $slug) use ($collection) {
					$items = collect($items);
					$items->count() && $items->each(function ($items, $entry) use ($collection, $slug) {
						$collection->put($entry, $items);
					});
				});

				return $collection->all();
			}
		);
	}
}
