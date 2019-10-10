<?php namespace Poppy\System\Rbac\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Rbac Facade
 */
class RbacFacade extends Facade
{
	/**
	 * @return string
	 */
	protected static function getFacadeAccessor(): string
	{
		return 'system.rbac';
	}
}

