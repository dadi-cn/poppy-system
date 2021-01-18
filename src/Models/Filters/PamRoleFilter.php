<?php namespace Poppy\System\Models\Filters;

use EloquentFilter\ModelFilter;

/**
 * 角色filter
 */
class PamRoleFilter extends ModelFilter
{
    /**
     * 根据ID查询
     * @param int $id 角色Id
     * @return PamRoleFilter
     */
    public function id($id)
    {
        return $this->where('id', $id);
    }

    /**
     * 根据name查询
     * @param string $name 角色名称
     * @return PamRoleFilter
     */
    public function name($name)
    {
        return $this->where('name', $name);
    }

    /**
     * 根据类型查询
     * @param string $type 类型
     * @return PamRoleFilter
     */
    public function type($type)
    {
        return $this->where('type', $type);
    }
}