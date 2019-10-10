<?php namespace Poppy\System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Support\Abstracts\Repository;

/**
 * Class PageRepository.
 */
class ModulesPage extends Repository
{

	/**
	 * Initialize.
	 * @param Collection $slugs 集合
	 */
	public function initialize(Collection $slugs)
	{
		$this->items = sys_cache('system')->rememberForever(
			'system.module.repo.page',
			function () use ($slugs) {
				$collection = collect();
				$slugs->each(function ($items, $slug) use ($collection) {
					if ($items) {
						$collection->put('module.' . $slug, $items);
					}
				});
				$collection->transform(function ($definition) {
					data_set($definition, 'tabs', collect($definition['tabs'])->map(function ($definition) {
						data_set($definition, 'fields', collect($definition['fields'])->map(function ($definition) {
							$setting = sys_setting($definition['key'], '');
							if (isset($definition['format'])) {
								switch ($definition['format']) {
									case 'boolean':
										$definition['value'] = (bool) $setting;
										break;
									default:
										$definition['value'] = $setting;
										break;
								}
							}
							else {
								$definition['value'] = $setting;
							}

							return $definition;
						}));

						return $definition;
					}));

					return $definition;
				});

				return $collection->all();
			}
		);
	}
}
