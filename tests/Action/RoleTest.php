<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\System\Action\Role;
use Poppy\System\Models\Filters\PamRoleFilter;
use Poppy\System\Models\PamRole;

class RoleTest extends TestCase
{
	public function testCreate()
	{
		$role = new Role();
		$item = $role->establish([
			'name'  => 'abc',
			'title' => 'abc',
			'type'  => 'backend',
		]);

		dd($role->getError());
	}

	public function testList()
	{
		$Db = PamRole::filter([], PamRoleFilter::class);

		return PamRole::paginationInfo($Db, function ($item) {
			return $item;
		});
	}

	public function testPermission()
	{
		$Role = (new Role());
		if (!($permission = $Role->permissions(45, false))) {
			dd($Role->getError());
		}
		else {
			dd($permission);
		}
	}
}
