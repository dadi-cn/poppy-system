<?php namespace Poppy\System\Classes\Grid\Concerns;

use Closure;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Poppy\System\Classes\Grid\Filter;

/**
 * 是否开启筛选
 */
trait HasFilter
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * 禁用筛选
     *
     * @param bool $disable
     * @return $this
     */
    public function disableFilter(bool $disable = true): self
    {
        $this->tools->disableFilterButton($disable);

        return $this->option('show_filter', !$disable);
    }

    /**
     * 获取筛选
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * 执行查询器
     * @param bool $toArray
     * @return array|Collection|mixed
     */
    public function applyFilter($toArray = true)
    {
        if ($this->builder) {
            call_user_func($this->builder, $this);
        }
        return $this->filter->execute($toArray);
    }

    /**
     * Set the grid filter.
     * @param Closure $callback
     */
    public function filter(Closure $callback)
    {
        call_user_func($callback, $this->filter);
    }

    /**
     * Render the grid filter.
     *
     * @return Factory|View|string
     */
    public function renderFilter()
    {
        if (!$this->option('show_filter')) {
            return '';
        }

        return $this->filter->render();
    }

    /**
     * 显示筛选
     *
     * @return $this
     */
    public function expandFilter()
    {
        $this->filter->expand();

        return $this;
    }

    /**
     * 初始化筛选
     *
     * @return $this
     */
    protected function initFilter()
    {
        $this->filter = new Filter($this->model());

        return $this;
    }
}
