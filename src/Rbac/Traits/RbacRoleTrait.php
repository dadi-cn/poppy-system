<?php namespace Poppy\System\Rbac\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamPermission;
use Poppy\System\Models\PamPermissionRole;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\PamRoleAccount;

/**
 * 角色 Trait
 */
trait RbacRoleTrait
{
	//Big block of caching functionality.

	/**
	 * @return Collection|mixed
	 */
	public function cachedPermissions()
	{
		static $cache;
		$rolePrimaryKey = $this->primaryKey;
		$cacheKey       = 'rbac_permissions_for_role_' . $this->$rolePrimaryKey;
		if (!isset($cache[$cacheKey])) {
			$cache[$cacheKey] = sys_cache('system:rbac_role')->remember($cacheKey, config('cache.ttl'), function () {
				return $this->perms()->get();
			});
		}

		return $cache[$cacheKey];
	}

	/**
	 * @param array $options 选项
	 * @return bool
	 */
	public function save(array $options = [])
	{   //both inserts and updates
		if (!parent::save($options)) {
			return false;
		}
		$this->flushPermissionRole();

		return true;
	}

	/**
	 * @param array $options 选项
	 * @return bool
	 */
	public function delete(array $options = [])
	{   //soft or hard
		if (!parent::delete($options)) {
			return false;
		}
		$this->flushPermissionRole();

		return true;
	}

	/**
	 * @return bool
	 */
	public function restore()
	{   //soft delete undo's
		if (!parent::restore()) {
			return false;
		}
		$this->flushPermissionRole();

		return true;
	}

	/**
	 * 清理权限
	 */
	public function flushPermissionRole()
	{
		sys_cache('system:rbac_role')->flush();
	}

	/**
	 * Many-to-Many relations with the user model.
	 * @return BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(
			PamAccount::class,
			(new PamRoleAccount())->getTable(),
			'role_id',
			'account_id'
		);
	}

	/**
	 * Many-to-Many relations with the permission model.
	 * Named "perms" for backwards compatibility. Also because "perms" is short and sweet.
	 * @return BelongsToMany
	 */
	public function perms()
	{
		return $this->belongsToMany(
			PamPermission::class,
			$this->getPermissionRoleTable(),
			'role_id',
			'permission_id'
		);
	}

	/**
	 * Boot the role model
	 * Attach event listener to remove the many-to-many records when trying to delete
	 * Will NOT delete any records if the role model uses soft deletes.
	 * @return void|bool
	 */
	public static function boot()
	{
		parent::boot();

		static::deleting(function ($role) {
			if (!method_exists((new PamRole()), 'bootSoftDeletes')) {
				$role->users()->sync([]);
				$role->perms()->sync([]);
			}
			return true;
		});
	}

	/**
	 * Checks if the role has a permission by its name.
	 * @param string|array $name       permission name or array of permission names
	 * @param bool         $requireAll all permissions in the array are required
	 * @return bool
	 */
	public function hasPermission($name, $requireAll = false)
	{
		if (is_array($name)) {
			foreach ($name as $permissionName) {
				$hasPermission = $this->hasPermission($permissionName);

				if ($hasPermission && !$requireAll) {
					return true;
				}

				if (!$hasPermission && $requireAll) {
					return false;
				}
			}

			// If we've made it this far and $requireAll is FALSE, then NONE of the permissions were found
			// If we've made it this far and $requireAll is TRUE, then ALL of the permissions were found.
			// Return the value of $requireAll;
			return $requireAll;
		}

		foreach ($this->cachedPermissions() as $permission) {
			if ($permission->name === $name) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Save the inputted permissions.
	 * @param mixed $inputPermissions 需要保存的权限
	 * @return void
	 */
	public function savePermissions($inputPermissions)
	{
		if (!empty($inputPermissions)) {
			$this->perms()->sync($inputPermissions);
		}
		else {
			$this->perms()->detach();
		}
	}

	/**
	 * Attach permission to current role.
	 * @param object|array $permission 权限
	 * @return void
	 */
	public function attachPermission($permission)
	{
		if (is_object($permission)) {
			$permission = $permission->getKey();
		}

		if (is_array($permission)) {
			$permission = $permission['id'];
		}

		$this->perms()->attach($permission);
	}

	/**
	 * Detach permission from current role.
	 * @param object|array $permission 权限
	 * @return void
	 */
	public function detachPermission($permission)
	{
		if (is_object($permission)) {
			$permission = $permission->getKey();
		}

		if (is_array($permission)) {
			$permission = $permission['id'];
		}

		$this->perms()->detach($permission);
	}

	/**
	 * Attach multiple permissions to current role.
	 * @param array $permissions 权限
	 * @return void
	 */
	public function attachPermissions($permissions)
	{
		foreach ($permissions as $permission) {
			$this->attachPermission($permission);
		}
	}

	/**
	 * Detach multiple permissions from current role
	 * @param array $permissions 权限
	 * @return void
	 */
	public function detachPermissions($permissions)
	{
		foreach ($permissions as $permission) {
			$this->detachPermission($permission);
		}
	}

	/**
	 * @return string
	 */
	private function getPermissionRoleTable()
	{
		return (new PamPermissionRole())->getTable();
	}
}