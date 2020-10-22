<?php namespace Poppy\System\Classes\Ui;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Classes\Traits\PamTrait;
use Request;
use View;

/**
 * 设置生成
 */
class PageUi
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

	/**
	 * SettingManager constructor.
	 * @param string $key chat.chat_gift
	 * @throws ApplicationException
	 */
	public function __construct($key)
	{
		try {
			$module     = Str::before($key, '.');
			$model      = Str::after($key, '.');
			$file       = poppy_path($module, 'src/models/definitions/' . $model . '/establish.yaml');
			$definition = app('poppy.yaml')->parseFile($file);

			$this->title  = $definition['title'];
			$this->fields = $definition['fields'];
		} catch (Exception $e) {
			throw new ApplicationException('配置文件不正确, 原因' . $e->getMessage());
		}

		$this->url = $this->url ?: Request::url();
	}

	public function setValue($values)
	{
		$this->values = $values;
	}

	/**
	 * @return Factory|\Illuminate\View\View
	 */
	public function render()
	{
		$fields = [];
		foreach ($this->fields as $f_key => $field) {
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
			$field['options']       = $form;
			$field['name']          = $f_key;
			$field['value']         = data_get($this->values, $f_key);
			$fields[$field['name']] = $field;
		}
		View::share([
			'title'       => $this->title,
			'_title'      => $this->title,
			'description' => $this->description ?? $this->title,
			'fields'      => $fields,
			'url'         => $this->url,
			'pam'         => $this->pam,
		]);
		return view('poppy-system::backend.tpl.ui_page');
	}
}