<?php namespace Poppy\System\Classes\Ui;

use Exception;
use Illuminate\Contracts\View\Factory;
use Input;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Classes\Traits\PamTrait;
use View;

/**
 * 设置生成
 */
class ListUi
{
	use AppTrait, PamTrait;


	/**
	 * 标题
	 * @var string
	 */
	private $title = '';

	/**
	 * Tabs
	 * @var array
	 */
	private $fields = [];

	/**
	 * 存在的值
	 * @var array|object
	 */
	private $values;

	/**
	 * Flat Tab
	 * @var array
	 */
	private $flatTab = [];

	/**
	 * 请求地址
	 * @var
	 */
	private $url;


	private $pagination;

	/**
	 * @var array 操作
	 */
	private $handle;

	/**
	 * @var array 搜索条件
	 */
	private $search;

	/**
	 * 输入信息
	 * @var array|string
	 */
	private $input;

	/**
	 * @var string 过滤
	 */
	private $filter;

	/**
	 * @var string 模型定义
	 */
	private $model;

	/**
	 * SettingManager constructor.
	 * @param string $key chat.chat_gift
	 * @throws ApplicationException
	 */
	public function __construct($key)
	{
		try {
			$module     = str_before($key, '.');
			$model      = str_after($key, '.');
			$file       = poppy_path($module, 'src/models/definitions/' . $model . '/lists.yaml');
			$definition = app('poppy.yaml')->parseFile($file);

			$this->title  = $definition['title'] ?? '-';
			$this->model  = $definition['model'] ?? '';
			$this->filter = $definition['filter'] ?? '';
			$this->input  = input();
			$this->fields = $definition['fields'];
			$this->search = $definition['search'] ?? [];
			$this->handle = $definition['handle'] ?? [];
		} catch (Exception $e) {
			throw new ApplicationException('配置文件不正确, 原因' . $e->getMessage());
		}

		$this->url = $this->url ?: Input::url();
	}

	/**
	 * 使用分页来进行渲染
	 * @param $pagination
	 * @return ListUi
	 */
	public function withPagination($pagination): ListUi
	{
		$this->pagination = $pagination;
		return $this;
	}

	/**
	 * 使用定义项目来显示分页
	 * @param array $input    查询条件
	 * @param int   $pagesize 分页
	 * @return ListUi
	 * @throws ApplicationException
	 */
	public function withDefinition($input = [], $pagesize = 15): ListUi
	{
		if ($input) {
			$this->input = $input;
		}
		$model = new $this->model;

		/* 检测是否支持过滤, 不支持过滤不进行显示
		 * ---------------------------------------- */
		if (!is_callable([$model, 'scopeFilter'])) {
			throw new ApplicationException('不支持非过滤器调用定义组件');
		}
		$this->pagination = $model->filter($input, $this->filter)->paginateFilter($pagesize);
		return $this;
	}

	/**
	 * @return Factory|\Illuminate\View\View
	 */
	public function render()
	{
		$fields = $this->fields;
		foreach ($this->search as $f_key => $field) {
			$field['type'] = $field['type'] ?? 'input';
			if ($field['type'] === 'input') {
				$form = [
					'class'       => 'layui-input',
					'placeholder' => $field['placeholder'] ?? '',
				];
			}
			elseif ($field['type'] === 'textarea') {
				$form = [
					'class'       => 'layui-textarea',
					'rows'        => '3',
					'placeholder' => $field['placeholder'] ?? '',
				];
			}
			else {
				$form = [];
			}
			$field['options']     = $form;
			$field['value']       = input($field['name']);
			$this->search[$f_key] = $field;
		}

		View::share([
			'_title'      => $this->title,
			'title'       => $this->title,
			'description' => $this->description ?? $this->title,
			'items'       => $this->pagination,
			'fields'      => $fields,
			'search'      => $this->search,
			'handle'      => $this->handle,
			'url'         => $this->url,
			'pam'         => $this->pam,
		]);
		return view('system::backend.tpl.ui_list');
	}

	/**
	 * 解析返回参数
	 * @param object|array $model
	 * @param array        $params
	 * @return array
	 */
	public static function params($model, $params = []): array
	{
		$data = [];
		collect($params)->each(function ($key) use ($model, &$data) {
			$data[$key] = data_get($model, $key);
		});
		return $data;
	}
}