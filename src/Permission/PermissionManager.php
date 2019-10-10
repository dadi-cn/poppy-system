<?php namespace Poppy\System\Permission;

use Auth;
use Illuminate\Support\Collection;
use Poppy\System\Models\PamAccount;
use Poppy\System\Module\Module;
use Poppy\System\Permission\Repositories\PermissionRepository;

/**
 * 权限管理器
 */
class PermissionManager
{

	public const CACHE_KEY_NAME = 'system.permission_key';

	/**
	 * @var PermissionRepository
	 */
	protected $repository;

	/**
	 * check permission
	 * @param string $permission 需要检测权限
	 * @param string $guard      保护的 guard
	 * @return bool
	 */
	public function check($permission, $guard): bool
	{
		/** @var PamAccount $user */
		$user = Auth::guard($guard)->user();
		if (!$user) {
			return false;
		}

		return $user->capable($permission);
	}

	/**
	 * @return PermissionRepository
	 */
	public function repository(): PermissionRepository
	{
		if (!$this->repository instanceof PermissionRepository) {
			$this->repository = new PermissionRepository();
			$collection       = collect();
			app('module')->enabled()->each(function (Module $module) use ($collection) {
				if ($module->offsetExists('permissions')) {
					$collection->put($module->slug(), $module->get('permissions'));
				}
			});
			$this->repository->initialize($collection);
		}

		return $this->repository;
	}

	/**
	 * Get all permissions.
	 * @return Collection|Permission[]
	 */
	public function permissions()
	{
		$perms = collect();
		$this->repository()->each(function ($permissions, $module) use ($perms) {
			collect($permissions)->each(function ($root) use ($perms, $module) {
				$rootSlug  = $root['slug'] ?? '';
				$rootTitle = $root['title'] ?? '';
				if (!$rootSlug) {
					return;
				}
				$typeSlug = PamAccount::GUARD_BACKEND;
				if (strpos($rootSlug, ':') !== false) {
					[$typeSlug, $rootSlug] = explode(':', $rootSlug);
				}
				$groups = collect($root['groups'] ?? []);
				$groups->each(function ($group) use ($perms, $module, $typeSlug, $rootSlug, $rootTitle) {
					$groupSlug  = $group['slug'] ?? '';
					$groupTitle = $group['title'] ?? '';
					if (!$groupSlug) {
						return;
					}
					$permissions = collect($group['permissions'] ?? []);
					$permissions->each(
						function ($permission) use ($perms, $module, $typeSlug, $rootSlug, $groupSlug, $groupTitle, $rootTitle) {
							$permissionSlug = $permission['slug'] ?? '';
							if (!$permissionSlug) {
								return;
							}
							$permission['root_title']  = $rootTitle;
							$permission['group_title'] = $groupTitle;
							$permission['module']      = $module;
							$permission['root']        = $rootSlug;
							$permission['type']        = $typeSlug;
							$permission['group']       = $groupSlug;
							$id                        = "{$typeSlug}:{$rootSlug}.{$groupSlug}.{$permissionSlug}";
							$perms->put($id, new Permission($permission, $id));
						}
					);
				});
			});
		});

		return $perms;
	}

	/**
	 * @param string $permission 权限
	 * @param bool   $cache      是否读取缓存
	 * @return bool
	 */
	public function has($permission, $cache = true): bool
	{
		$minute = $cache ? 60 * 24 : 0;
		/** @var Collection $permissions */
		$permissions = sys_cache('system')->remember(self::CACHE_KEY_NAME, $minute, function () {
			return $this->permissions()->keys();
		});

		return $permissions->contains($permission);
	}


	/**
	 * Get default permission by group
	 * @param string $group 获取分组
	 * @return Collection
	 */
	public function defaultPermissions($group): Collection
	{
		$permissions = collect([]);
		$this->permissions()->each(function (Permission $permission) use ($permissions, $group) {
			if ($permission->type() === $group && $permission->isDefault()) {
				$permissions->push($permission->key());
			}
		});

		return $permissions;
	}
}
