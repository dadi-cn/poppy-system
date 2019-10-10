<?php namespace Poppy\System\Permission\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamPermission;
use Poppy\System\Models\PamRole;
use Poppy\System\Permission\Permission;

/**
 * Class PermissionCommand.
 */
class PermissionCommand extends Command
{

	protected $signature = 'system:permission
		{do : The permission action to handle, allow <lists,init>}
		{--permission= : The permission need to check}
		';

	protected $description = 'Permission manage list.';

	/**
	 * @var string Display Key;
	 */
	private $key;

	/**
	 * Command Handler.
	 * @return bool
	 * @throws Exception
	 */
	public function handle()
	{
		$action    = $this->argument('do');
		$this->key = $action;
		switch ($action) {
			case 'lists':
				$this->lists();
				break;
			case 'init':
				$this->init();
				break;
			case 'menus':
				$this->checkMenus();
				break;
			case 'assign':
				$this->assign();
				break;
			case 'check':
				$permission = $this->option('permission');
				$this->checkPermission($permission);
				break;
			default:
				$this->error(
					sys_mark('system', self::class, ' Command Not Exists!')
				);
				break;
		}

		return true;
	}

	public function lists()
	{
		$data = new Collection();
		app('permission')->permissions()->each(function (Permission $permission) use ($data) {
			$data->push([
				$permission->type(),
				$permission->key(),
				$permission->description(),
			]);
		});
		$this->table(
			['Type', 'Identification', 'Description'],
			$data->toArray()
		);
	}

	/**
	 * @throws Exception
	 */
	public function init()
	{
		$this->call('poppy:optimize');

		// get all permission
		$permissions = app('permission')->permissions();
		if (!$permissions) {
			$this->info($this->key . 'No permission need import.');

			return;
		}

		// 删除多余权限
		PamPermission::whereNotIn('name', $permissions->keys())->delete();

		// insert db
		foreach ($permissions as $key => $permission) {
			PamPermission::updateOrCreate([
				'name' => $key,
			], [
				'name'        => $key,
				'title'       => $permission->description(),
				'type'        => $permission->type(),
				'group'       => $permission->group(),
				'module'      => $permission->module(),
				'root'        => $permission->root(),
				'description' => '',
			]);
		}
		$endNum = PamPermission::count();

		// 所有后台权限都赋值给　ｒｏｏｔ　用户组
		// 然后清空角色缓存表，　使角色定义生效
		$permissions = PamPermission::where('type', PamAccount::TYPE_BACKEND)->get();

		/** @var PamRole $role */
		$role = PamRole::where('name', PamRole::BE_ROOT)->first();
		$role->savePermissions($permissions);
		$role->flushPermissionRole();

		$this->info(
			sys_mark('system', self::class, 'Import permission Success! ' . $endNum . ' items;')
		);
		$this->call('poppy:optimize');
	}

	/**
	 * 将权限赋值给指定的用户组
	 */
	private function assign()
	{
		$name            = $this->ask('Which role you want assign permission ?');
		$permission_type = $this->ask('Which permission you want to get ?');
		$role            = PamRole::where('name', $name)->first();

		if (!$role) {
			$this->error(
				sys_mark('system', self::class, 'Role [' . $name . '] not exists in table !')
			);

			return;
		}

		$permissions = PamPermission::where('type', $permission_type)->get();
		if (!$permissions) {
			$this->error(
				sys_mark('system', self::class, 'Permission type [' . $permission_type . '] has no permissions !')
			);

			return;
		}
		$role->savePermissions($permissions);
		$role->flushPermissionRole();
		$this->info("\nSave [{$permission_type}] permission to role [{$name}] !");
	}

	/**
	 * @param string $permission 需要检测的权限
	 */
	private function checkPermission($permission)
	{
		if (PamPermission::where('name', $permission)->exists()) {
			$this->info(
				sys_mark('system', self::class, 'Permission `' . $permission . '` in table ')
			);
		}
		else {
			$this->error(
				sys_mark('system', self::class, 'Permission `' . $permission . '` not in table')
			);
		}
	}

	/**
	 * 检查菜单
	 */
	private function checkMenus()
	{
		// clear cache
		sys_cache('system')->flush();

		// calc
		$navigations = app('module')->menus();
		$format      = function ($item) {
			return [
				'title'      => $item['text'],
				'parent'     => $item['parent'],
				'permission' => $item['permission'],
			];
		};

		$faults = collect();
		$navigations->each(function ($item) use ($faults, $format) {
			// 订单 / 系统
			$permission = $item['permission'] ?? '';
			if ($permission && !app('permission')->has($permission)) {
				$faults->push($format($item));
			}
			// 分组
			$children = collect((array) $item['children']);
			$children->map(function ($item) use ($faults, $format) {
				$permission = $item['permission'] ?? '';
				if ($permission && !app('permission')->has($permission)) {
					$faults->push($format($item));
				}
				$children = collect((array) $item['children']);
				// 路由
				$children->each(function ($item) use ($faults, $format) {
					$permission = $item['permission'] ?? '';
					if ($permission && !app('permission')->has($permission)) {
						$faults->push($format($item));
					}
				});
			});
		});

		if (!$faults->count()) {
			$this->info(
				sys_mark('system', self::class, 'All Permission are right.')
			);
		}
		else {
			$this->warn(
				sys_mark('system', self::class, 'Error Permission in menus:')
			);
			$this->table(
				['Title', 'Parent', 'Permission'],
				$faults->toArray()
			);
		}
	}
}
