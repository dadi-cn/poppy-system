<?php namespace Poppy\System\Classes\Grid\Tools;

use Poppy\System\Classes\Grid\Filter;

/**
 * 筛选按钮
 */
class FilterButton extends AbstractTool
{
    /**
     * @var string
     */
    protected $view = 'py-system::tpl.filter.button';

    /**
     * @var string
     */
    protected $btnClassName;

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $variables = [
            'scopes'        => $this->filter()->getScopes(),
            'current_label' => $this->getCurrentScopeLabel(),
            'url_no_scopes' => $this->filter()->urlWithoutScopes(),
            'btn_class'     => $this->getElementClassName(),
            'expand'        => $this->filter()->expand,
            'filter_id'     => $this->filter()->getFilterId(),
        ];

        return view($this->view, $variables)->render();
    }

    /**
     * @return Filter
     */
    protected function filter()
    {
        return $this->grid->getFilter();
    }

    /**
     * Get button class name.
     *
     * @return string
     */
    protected function getElementClassName()
    {
        if (!$this->btnClassName) {
            $this->btnClassName = uniqid() . '-filter-btn';
        }

        return $this->btnClassName;
    }


    /**
     * @return mixed
     */
    protected function renderScopes()
    {
        return $this->filter()->getScopes()->map->render()->implode("\r\n");
    }

    /**
     * Get label of current scope.
     *
     * @return string
     */
    protected function getCurrentScopeLabel()
    {
        if ($scope = $this->filter()->getCurrentScope()) {
            return "&nbsp;{$scope->getLabel()}&nbsp;";
        }

        return '';
    }
}