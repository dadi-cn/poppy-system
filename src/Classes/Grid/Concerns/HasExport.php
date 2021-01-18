<?php namespace Poppy\System\Classes\Grid\Concerns;

use Poppy\System\Classes\Grid\Exporter;
use Poppy\System\Classes\Grid\Exporters\AbstractExporter;
use Poppy\System\Classes\Grid\Tools\ExportButton;

trait HasExport
{

    /**
     * Export driver.
     *
     * @var string
     */
    protected $exporter;


    /**
     * 是否显示导出按钮
     */
    public function isShowExport()
    {
        return $this->option('show_exporter');
    }

    /**
     * Disable export.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableExport(bool $disable = true)
    {
        return $this->option('show_exporter', !$disable);
    }

    /**
     * Render export button.
     *
     * @return string
     */
    public function renderExportButton()
    {
        return (new ExportButton($this))->render();
    }


    /**
     * Set exporter driver for Grid to export.
     *
     * @param $exporter
     *
     * @return $this
     */
    public function exporter($exporter)
    {
        $this->exporter = $exporter;

        return $this;
    }

    /**
     * Get the export url.
     *
     * @param int  $scope
     * @param null $args
     *
     * @return string
     */
    public function getExportUrl($scope = 1, $args = null)
    {
        $input = array_merge(request()->all(), Exporter::formatExportQuery($scope, $args));

        if ($constraints = $this->model()->getConstraints()) {
            $input = array_merge($input, $constraints);
        }

        return $this->resource() . '?' . http_build_query($input);
    }

    /**
     * Handle export request.
     *
     * @param bool $forceExport
     */
    protected function handleExportRequest($forceExport = false)
    {
        if (!$scope = request(Exporter::$queryName)) {
            return;
        }

        // clear output buffer.
        if (ob_get_length()) {
            ob_end_clean();
        }

        $this->model()->usePaginate(false);

        if ($this->builder) {
            call_user_func($this->builder, $this);

            $this->getExporter($scope)->export();
        }

        if ($forceExport) {
            $this->getExporter($scope)->export();
        }
    }

    /**
     * @param string $scope
     *
     * @return AbstractExporter
     */
    protected function getExporter($scope)
    {
        return (new Exporter($this))->resolve($this->exporter)->withScope($scope);
    }
}
