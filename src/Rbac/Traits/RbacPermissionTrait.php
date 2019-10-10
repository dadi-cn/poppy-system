<?php namespace Poppy\System\Rbac\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Poppy\System\Models\PamPermission;
use Poppy\System\Models\PamPermissionRole;
use Poppy\System\Models\PamRole;

/**
 * 权限 trait
 */
trait RbacPermissionTrait
{
	/**
	 * Many-to-Many relations with role model.
	 * @return BelongsToMany
	 */
	public function roles()
	{
		return $this->belongsToMany(
			PamRole::class,
			(new PamPermissionRole())->getTable()
		);
	}

	/**
	 * Boot the permission model
	 * Attach event listener to remove the many-to-many records when trying to delete
	 * Will NOT delete any records if the permission model uses soft deletes.
	 * @return void|bool
	 */
	public static function boot()
	{
		parent::boot();

		static::deleting(function ($permission) {
			if (!method_exists(new PamPermission(), 'bootSoftDeletes')) {
				$permission->roles()->sync([]);
			}

			return true;
		});
	}
}

