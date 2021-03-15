<?php namespace Poppy\System\Classes;

use Closure;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Classes\Grid\Column;
use Poppy\System\Classes\Grid\Concerns;
use Poppy\System\Classes\Grid\Model;
use Poppy\System\Classes\Grid\Row;
use Poppy\System\Http\Lists\ListBase;
use Response;
use Throwable;

class Grid
{
    use Concerns\HasElementNames,
        Concerns\HasHeader,
        Concerns\HasExport,
        Concerns\HasFooter,
        Concerns\HasFilter,
        Concerns\HasTools,
        Concerns\HasTotalRow,
        Concerns\HasHotKeys,
        Concerns\HasActions,
        Concerns\HasSelector,
        Concerns\CanHidesColumns,
        Concerns\CanFixColumns,
        Concerns\LayDefines,
        Concerns\HasQuickButton;

    /**
     * All column names of the grid.
     *
     * @var array
     */
    public $columnNames = [];

    /**
     * Per-page options.
     *
     * @var array
     */
    public $perPages = [15, 20, 30, 50, 100];

    /**
     * Default items count per-page.
     *
     * @var int
     */
    public $perPage = 15;

    /**
     * @var string
     */
    public $tableId;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator = null;

    /**
     * The grid data model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * Collection of all grid columns.
     *
     * @var Collection
     */
    protected $columns;

    /**
     * Collection of all data rows.
     *
     * @var Collection
     */
    protected $rows;

    /**
     * Rows callable fucntion.
     *
     * @var Closure
     */
    protected $rowsCallback;

    /**
     * Grid builder.
     *
     * @var Closure
     */
    protected $builder;

    /**
     * Mark if the grid is builded.
     *
     * @var bool
     */
    protected $builded = false;

    /**
     * All variables in grid view.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Resource path of the grid.
     *
     * @var
     */
    protected $resourcePath;

    /**
     * Default primary key name.
     *
     * @var string
     */
    protected $keyName = 'id';


    /**
     * View for grid to render.
     *
     * @var string
     */
    protected $view = 'py-system::tpl.grid.table';

    /**
     * @var []callable
     */
    protected $renderingCallbacks = [];
    /**
     * Options for grid.
     *
     * @var array
     */
    protected $options = [
        'show_pagination'      => true,
        'show_tools'           => true,
        'show_filter'          => true,
        'show_exporter'        => true,
        'show_actions'         => true,
        'show_row_selector'    => true,
        'show_create_btn'      => true,
        'show_column_selector' => true,
    ];
    /**
     * Initialization closure array.
     *
     * @var []Closure
     */
    protected static $initCallbacks = [];

    /**
     * Create a new grid instance.
     *
     * @param Eloquent     $model
     * @param Closure|null $builder
     */
    public function __construct(Eloquent $model, Closure $builder = null)
    {
        $this->model   = new Model($model, $this);
        $this->keyName = $model->getKeyName();
        $this->builder = $builder;

        $this->initialize();

        $this->handleExportRequest();

        $this->callInitCallbacks();
    }

    /**
     * Get Grid model.
     *
     * @return Eloquent|Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Initialize.
     */
    protected function initialize()
    {
        $this->tableId = uniqid('grid-table');

        $this->columns = Collection::make();
        $this->rows    = Collection::make();

        $this->initTools($this);
        $this->initFilter();
    }

    /**
     * Initialize with user pre-defined default disables and exporter, etc.
     *
     * @param Closure|null $callback
     */
    public static function init(Closure $callback = null)
    {
        static::$initCallbacks[] = $callback;
    }

    /**
     * Call the initialization closure array in sequence.
     */
    protected function callInitCallbacks()
    {
        if (empty(static::$initCallbacks)) {
            return;
        }

        foreach (static::$initCallbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * @param string $grid_class
     * @param string $field
     * @param string $order
     * @throws ApplicationException
     */
    public function setLists(string $grid_class, $field = 'id', $order = 'desc')
    {
        if (!class_exists($grid_class)) {
            throw new ApplicationException('Grid Class `' . $grid_class . '` Not Exists.');
        }

        /** @var ListBase $List */
        $List = new $grid_class($this);
        $List->columns();
        $List->actions();
        $this->columns = $List->getColumns();
        if (method_exists($this->model(), 'orderBy')) {
            $this->model()->orderBy($field, $order);
        }

        $this->filter($List->seek());
        $this->appendQuickButton($List->quickButtons());
    }

    /**
     * Get or set option for grid.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this|mixed
     */
    public function option($key, $value = null)
    {
        if (is_null($value)) {
            return $this->options[$key];
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName ?: 'id';
    }


    /**
     * Paginate the grid.
     *
     * @param int $perPage
     *
     * @return void
     */
    public function paginate($perPage = 20)
    {
        $this->perPage = $perPage;

        $this->model()->setPerPage($perPage);
    }

    /**
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator()
    {
        $this->paginator = $this->model()->eloquent();

        if ($this->paginator instanceof LengthAwarePaginator) {
            $this->paginator->appends(request()->all());
        }
        return $this->paginator;
    }

    /**
     * Disable grid pagination.
     *
     * @param bool $disable
     * @return $this
     */
    public function disablePagination(bool $disable = true)
    {
        $this->model->usePaginate(!$disable);

        return $this->option('show_pagination', !$disable);
    }

    /**
     * If this grid use pagination.
     *
     * @return bool
     */
    public function showPagination()
    {
        return $this->option('show_pagination');
    }

    /**
     * 设置分页的可选条目数
     *
     * @param array $perPages
     */
    public function perPages(array $perPages)
    {
        $this->perPages = $perPages;
    }

    /**
     * Disable row selector.
     *
     * @param bool $disable
     * @return Grid|mixed
     */
    public function disableRowSelector(bool $disable = true): self
    {
        return $this->disableBatchActions($disable);
    }

    /**
     * 添加多选框列
     *
     * @return void
     */
    protected function prependRowSelectorColumn()
    {
        if (!$this->option('show_row_selector')) {
            return;
        }

        $this->layPrependRowSelectorColumn();
    }

    /**
     * Apply column filter to grid query.
     *
     * @return void
     */
    protected function applyColumnFilter()
    {
        $this->columns->each->bindFilterQuery($this->model());
    }

    /**
     * Apply column search to grid query.
     *
     * @return void
     */
    protected function applyColumnSearch()
    {
        $this->columns->each->bindSearchQuery($this->model());
    }

    /**
     * @return array|Collection|mixed
     */
    protected function applyQuery()
    {
        $this->applyQuickSearch();

        $this->applyColumnFilter();

        $this->applyColumnSearch();

        $this->applySelectorQuery();

        return $this->applyFilter(false);
    }

    /**
     * 添加多选 / 操作项目
     * @return void
     */
    protected function addDefaultColumns()
    {
        $this->prependRowSelectorColumn();
    }

    /**
     * Build the grid.
     *
     * @return void
     */
    public function build()
    {
        if ($this->builded) {
            return;
        }

        $this->addDefaultColumns();

        $this->builded = true;
    }


    /**
     * 查询并返回数据
     * @param int $pagesize
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|Resp|Response
     */
    public function inquire($pagesize = 15)
    {
        $this->paginate($pagesize);
        /**
         * 获取到的模型数据
         */
        $collection = $this->applyQuery();

        $this->build();

        Column::setOriginalGridModels($collection);

        $data = $collection->toArray();
        $this->columns->map(function (Column $column) use (&$data) {
            $data = $column->fill($data);

            $this->columnNames[] = $column->getName();
        });

        $this->buildRows($data);

        $rows = [];
        foreach ($this->rows() as $row) {
            $item = [];
            foreach ($this->visibleColumnNames() as $name) {
                $item[$name] = $row->column($name);
            }
            $rows[] = $item;
        }

        $paginator = $this->paginator();

        return Resp::success('获取成功', [
            'list'       => $rows,
            'pagination' => [
                'total' => $paginator->total(),
                'page'  => $paginator->currentPage(),
                'size'  => $paginator->perPage(),
                'pages' => $paginator->lastPage(),
            ],
            '_json'      => 1,
        ]);
    }

    /**
     * Build the grid rows.
     *
     * @param array $data
     *
     * @return void
     */
    protected function buildRows(array $data)
    {
        $this->rows = collect($data)->map(function ($model, $number) {
            return new Row($number, $model, $this->keyName);
        });

        if ($this->rowsCallback) {
            $this->rows->map($this->rowsCallback);
        }
    }

    /**
     * Set grid row callback function.
     *
     * @param Closure|null $callable
     *
     * @return Collection|null
     */
    public function rows(Closure $callable = null)
    {
        if (is_null($callable)) {
            return $this->rows;
        }

        $this->rowsCallback = $callable;
    }


    /**
     * Get current resource url.
     *
     * @param string $path
     *
     * @return string
     */
    public function resource($path = null)
    {
        if (!empty($path)) {
            $this->resourcePath = $path;

            return $this;
        }

        if (!empty($this->resourcePath)) {
            return $this->resourcePath;
        }

        return url(app('request')->getPathInfo());
    }

    /**
     * Add variables to grid view.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function with($variables = [])
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Get all variables will used in grid view.
     *
     * @return array
     */
    protected function variables()
    {
        $this->variables['grid']      = $this;
        $this->variables['id']        = $this->tableId;
        $this->variables['filter_id'] = $this->getFilter()->getFilterId();
        $this->variables['scopes']    = $this->getFilter()->getScopes();
        $this->variables['lay']       = $this->layDefine();

        return $this->variables;
    }

    /**
     * Set a view to render.
     *
     * @param string $view
     * @param array  $variables
     */
    public function setView($view, $variables = [])
    {
        if (!empty($variables)) {
            $this->with($variables);
        }

        $this->view = $view;
    }

    /**
     * Set grid title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->variables['title'] = $title;

        return $this;
    }


    /**
     * Set resource path for grid.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setResource($path)
    {
        $this->resourcePath = $path;

        return $this;
    }

    /**
     * Set rendering callback.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function rendering(callable $callback)
    {
        $this->renderingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Call callbacks before render.
     *
     * @return void
     */
    protected function callRenderingCallback()
    {
        foreach ($this->renderingCallbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * Get the string contents of the grid view.
     *
     * @return string
     * @throws Throwable
     */
    public function render()
    {
        $this->handleExportRequest(true);

        $this->build();

        $this->callRenderingCallback();

        $this->layFormat();

        $variables = $this->variables();

        return view($this->view, $variables)->render();
    }
}
