<?php namespace Poppy\System\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Support\Abstracts\Repository;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

/**
 * Class MenuRepository.
 */
class ModulesMenu extends Repository
{

	/**
	 * @var Collection
	 */
	protected $structures;

	/**
	 * Initialize.
	 * @param Collection $uis 集合
	 */
	public function initialize(Collection $uis)
	{
		// check serve setting
		$this->items = sys_cache('system')->rememberForever(
			'system.module.repo.menus',
			function () use ($uis) {
				$uis = $uis->map(function ($definition) {
					// slug  - module
					// layer - module
					return collect($definition)->map(function ($definition, $key) {
						// layer - backend/develop/web
						$definition['type'] = $key;

						// new groups
						$parsedGroups = [];
						collect($definition['groups'])->each(function ($groups, $item_key) use (&$parsedGroups) {
							// layer - children
							$parsedGroup = $this->parseLink($groups);
							if (!is_null($parsedGroup)) {
								$parsedGroups[$item_key] = $parsedGroup;
							}
						})->toArray();
						$definition['groups'] = $parsedGroups;
						$definition['routes'] = collect($definition['groups'])->pluck('routes')->flatten();

						return $definition;
					})->toArray();
				});

				$collection = collect();
				$uis->each(function ($definition, $slug) use ($collection) {
					$this->parse($definition, $slug, $collection);
				});

				/* 对 Injection 进行处理
				 * ---------------------------------------- */
				$reCollection = collect();
				$collection->each(function ($definition, $slug) use ($reCollection) {
					$groups = $definition['groups'] ?? [];
					if ($groups) {
						collect($groups)->each(function ($group, $index) use ($reCollection, &$definition) {
							$injection = $group['injection'] ?? '';
							if (!$injection) {
								return;
							}
							[$key, $name] = explode('.', $injection);
							if ($reCollection->offsetExists($key)) {
								$item                                = $reCollection->get($key);
								$item['groups'][$name]['children'][] = $group;
								$reCollection->put($key, $item);
								unset($definition['groups'][$index]);
							}
						});
					}
					$reCollection->put($slug, $definition);
				});


				$collection = $reCollection;
				$handled    = collect();
				$collect    = collect();
				$collection->each(function ($definition, $key) use (&$handled, $collect) {
					/* 避免重复循环请求的数据错误
					 * ---------------------------------------- */
					if ($handled->contains($key)) {
						$definition = $collect->get($key);
					}
					$definition['routes'] = collect($definition['groups'])->pluck('routes')->flatten()->unique();
					$collect->put($key, $definition);
				});

				// 进行排序
				$collect->sortBy('order', SORT_ASC);

				return $collect->all();
			}
		);
	}

	/**
	 * 根据用户返回合适的菜单
	 * @param PamAccount $pam 用户
	 * @return Collection
	 */
	public function withPermission(PamAccount $pam): Collection
	{
		$type  = $pam->type;
		$menus = $this->where('type', $type);
		$menu  = collect();
		$menus->each(function ($module) use ($pam, $menu) {
			$groups = collect();
			collect($module['groups'])->each(function ($group) use ($pam, $groups) {
				$children = collect();

				//
				collect($group['children'])->each(function ($url) use ($children, $pam) {

					/* 三级菜单的权限
					 * ---------------------------------------- */
					if ($url['children'] ?? []) {
						$submenus = collect([]);
						collect($url['children'] ?? [])->each(function ($url) use ($submenus, $pam) {
							if ($url['permission'] ?? '') {
								// 管理员拥有所有权限
								if ($pam->type === PamAccount::TYPE_BACKEND && $pam->hasRole(PamRole::BE_ROOT)) {
									$submenus->push($url);
								}
								elseif ($pam->capable($url['permission'])) {
									$submenus->push($url);
								}
							}
							else {
								$submenus->push($url);
							}
						});
						if ($submenus->count()) {
							$url['children'] = $submenus->toArray();
							$children->push($url);
						}
					}

					if ($url['route'] ?? '') {
						if ($url['permission'] ?? '') {
							// 管理员拥有所有权限
							if ($pam->type === PamAccount::TYPE_BACKEND && $pam->hasRole(PamRole::BE_ROOT)) {
								$children->push($url);
							}
							elseif ($pam->capable($url['permission'])) {
								$children->push($url);
							}
						}
						else {
							$children->push($url);
						}
					}

				});
				$group['children'] = $children;
				$group['routes']   = collect($group['children'])->pluck('route')->flatten();

				// 重新匹配 match
				$matches = collect($group['children'])->pluck('match')->flatten()->filter();
				if ($matches->count()) {
					$group['routes'] = $group['routes']->merge($matches->toArray());
				}

				if (count($group['routes'])) {
					$groups->push($group);
				}
			});
			$module['groups'] = $groups;
			if (count($module['groups'])) {
				$menu->push($module);
			}
		});

		return $menu;
	}

	/**
	 * @param string $type  类型
	 * @param array  $perms perms
	 * @return Collection
	 */
	public function withType($type, $perms): Collection
	{
		$menus = $this->where('type', $type);
		$menu  = collect();
		$menus->each(function ($module) use ($menu, $perms) {
			$groups = collect();
			collect($module['groups'])->each(function ($group) use ($groups, $perms) {
				$children = collect();
				collect($group['children'])->each(function ($url) use ($children, $perms) {
					if (isset($url['permission']) && $url['permission'] && in_array($url['permission'], $perms, true)) {
						$children->push($url);
					}
				});
				$group['children'] = $children;
				$group['routes']   = collect($group['children'])->pluck('route')->flatten();

				// 重新匹配 match
				$matches = collect($group['children'])->pluck('match')->flatten()->filter();
				if ($matches->count()) {
					$group['routes'] = $group['routes']->merge($matches->toArray());
				}

				if (count($group['routes'])) {
					$groups->push($group);
				}
			});
			$module['groups'] = $groups;
			if (count($module['groups'])) {
				$menu->push($module);
			}
		});

		return $menu;
	}

	/**
	 * @param array      $items      数据数据
	 * @param string     $prefix     前缀
	 * @param Collection $collection 集合
	 */
	private function parse(array $items, string $prefix, Collection $collection): void
	{
		collect($items)->each(function ($definition, $key) use ($collection, $prefix) {
			$key = $prefix . '/' . $key;

			/* get manifest files
			 * ---------------------------------------- */
			$configuration         = app('poppy')->where('slug', $prefix);
			$definition['enabled'] = $configuration['enabled'] ?? false;
			$definition['order']   = $configuration['order'] ?? 0;
			$definition['text']    = $configuration['description'] ?? 0;
			$definition['parent']  = $prefix;
			if (isset($definition['children'])) {
				$this->parse($definition['children'], $key, $collection);
				unset($definition['children']);
			}
			$collection->put($key, $definition);
		});
	}

	/**
	 * 解析链接
	 * @param array $group 数据数组
	 * @return mixed
	 */
	private function parseLink($group)
	{
		if (isset($group['children']) && is_array($group['children'])) {
			// parse children
			$newGroup = [];
			foreach ($group['children'] as $key => $define) {
				$calc = $this->parseLink($define);
				if (!is_null($calc)) {
					$newGroup[$key] = $calc;
				}
			}
			$group['children'] = $newGroup;

			// parse match && routes
			$matches         = collect($group['children'])->pluck('match')->flatten()->filter();
			$group['routes'] = collect($group['children'])->pluck('route');
			if ($matches->count()) {
				$group['routes'] = $group['routes']->merge($matches->toArray());
			}
		}
		$route     = $group['route'] ?? '';
		$routeHide = (array) config('module.system.route_hide');
		if (in_array($route, $routeHide, false)) {
			return null;
		}
		$routeParam   = $group['route_param'] ?? '';
		$param        = $group['param'] ?? '';
		$url          = $route ? route_url($route, $routeParam, $param) : '#';
		$group['url'] = $url;
		unset($group['param'], $group['route_param']);
		$group['key'] = UtilHelper::md5($group);
		return $group;
	}
}
