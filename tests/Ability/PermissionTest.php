<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Models\PamPermission;
use Poppy\System\Models\PamRole;
use Poppy\System\Permission\Permission;
use Poppy\System\Tests\Base\SystemTestCase;

class PermissionTest extends SystemTestCase
{
	public function setUp(): void
	{
		parent::setUp();
		$this->initPam();
	}

	/**
	 * 检测权限不为空
	 */
	public function testPermissions(): void
	{
		$permissions = app('permission')->permissions();
		$items       = $permissions->map(function (Permission $permission) {
			return [
				$permission->module(),
				$permission->type(),
				$permission->key(),
				$permission->groupTitle(),
			];
		});

		// $this->table(['module', 'type', 'key', 'title'], $items);

		$this->assertNotEmpty($items);
	}

	/**
	 * 检测是否存在指定权限
	 */
	public function testHasPermission(): void
	{
		/** @var PamRole $role */
		$role = PamRole::where('name', 'user')->first();


		$key = 'backend:system.global.manage';

		/** @var Permission $permission */
		$permission = app('permission')->permissions()->offsetGet($key);

		if ($permission) {
			$dbPerm = PamPermission::where('name', $key)->first();
			$role->attachPermission($dbPerm);
			$role->save();
			if ($this->pam->capable($permission->key())) {
				$this->assertTrue(true);
			}
			else {
				$this->assertTrue(false, "没有 '{$permission->description()} '权限, 无权操作");
			}
		}
		else {
			$this->assertTrue(false, 'Permission Not Exists!');
		}
	}
}
