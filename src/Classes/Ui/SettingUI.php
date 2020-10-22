<?php namespace Poppy\System\Classes\Ui;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Classes\Traits\PamTrait;
use Validator;
use View;

/**
 * 表单设置生成器
 */
class SettingUI
{
	use AppTrait, PamTrait, KeyParserTrait, CoreTrait;

	/**
	 * @var string 需要处理的 addon.sms/poppy.system
	 */
	private $key;

	/**
	 * Init
	 * @var array
	 */
	private $initialization = [];

	/**
	 * 标题
	 * @var string
	 */
	private $title = '';

	/**
	 * 权限
	 * @var string
	 */
	private $permission = '';

	/**
	 * Tabs
	 * @var array
	 */
	private $tabs = [];

	/**
	 * 页面
	 * @var array
	 */
	private $pages = [];

	/**
	 * Flat Tab
	 * @var array
	 */
	private $flatTab = [];

	/**
	 * Path
	 * @var string
	 */
	private $path = '';

	/**
	 * 请求地址
	 * @var
	 */
	private $url;

	/**
	 * SettingManager constructor.
	 * @param $key
	 * @throws Exception
	 */
	public function __construct($key)
	{
		try {
			if (Str::startsWith($key, 'addon.')) {
				$this->pages = [];
				$folder      = Str::after($key, 'addon.');
				$file        = base_path('addons/' . $folder . '/configurations/pages.yaml');
				$definition  = app('poppy.yaml')->parseFile($file);

				foreach ($definition['tabs'] as $_key => $tab) {
					foreach ($tab['fields'] as $f_key => $field) {
						$field['value']        = sys_setting($field['key']);
						$tab['fields'][$f_key] = $field;
					}
					$definition['tabs'][$_key] = $tab;
				}
			}
			else {
				$this->pages = $this->coreModule()->pages();
				$definition  = $this->coreModule()->pages()->offsetGet($key);
			}

			$this->key   = $key;
			$this->title = $definition['title'];
			$this->tabs  = $definition['tabs'];
			$this->path  = $key;
		} catch (Exception $e) {
			throw new ApplicationException('配置文件不正确, 原因' . $e->getMessage());
		}

		foreach ($this->tabs as $_key => $tab) {
			foreach ($tab['fields'] as $f_key => $field) {
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
				$form             = array_merge($form, $this->getValidates($field, 'js-validation'));
				$field['options'] = $form;

				[$ns, $group, $item] = $this->parseKey($field['key']);
				unset($tab['fields'][$f_key]);
				if (!$ns || !$group || !$item) {
					continue;
				}
				$field['name']         = "{$ns}______________{$group}______________{$item}";
				$tab['fields'][$f_key] = $field;

				$this->flatTab[$field['name']] = $field;
			}
			$this->tabs[$_key] = $tab;
		}

		$this->url = $this->url ?: route_url();
	}

	/**
	 * @return Factory|\Illuminate\View\View
	 */
	public function render()
	{
		View::share([
			'title'       => $this->title,
			'description' => $this->description ?? $this->title,
			'tabs'        => $this->tabs,
			'pages'       => $this->pages,
			'url'         => $this->url,
			'path'        => $this->path,
			'pam'         => $this->pam,
			'_is_iframe'  => input('_iframe'),
		]);
		if (input('_iframe')) {
			return view('poppy-system::backend.tpl.setting_dialog');
		}
		return view('poppy-system::backend.tpl.setting_page');
	}

	/**
	 * 更新配置
	 * @param Request $request 请求
	 * @return bool
	 */
	public function save(Request $request): bool
	{
		$inputKeys = collect();
		$keys      = collect($request->keys());
		$configs   = [];
		$keys->each(function ($item) use ($inputKeys, $request, &$configs) {
			if (strpos($item, '______________') !== false) {
				$configs[$item] = $request->get($item);
				$inputKeys->push($item);
			}
		});

		$rule  = [];
		$title = [];
		foreach ($this->flatTab as $key => $field) {
			if (!in_array($key, $inputKeys->toArray(), true)) {
				continue;
			}
			$rule[$key]  = $this->getValidates($field, 'laravel');
			$title[$key] = Arr::get($field, 'label');
		}
		if ($rule) {
			$validator = Validator::make($configs, $rule, [], $title);
			if ($validator->fails()) {
				return $this->setError($validator->messages());
			}
		}

		foreach ($configs as $key => $value) {
			$key = Str::replaceFirst('______________', '::', $key);
			$key = Str::replaceFirst('______________', '.', $key);
			app('setting')->set($key, $value);
		}

		sys_cache('system')->forget('system.module.repo.page');

		[$type, $plugin] = explode('.', $this->key);
		if ($type === 'module') {
			sys_cache($plugin)->flush();
		}

		return true;
	}

	/**
	 * 返回验证项目
	 * @param array  $field       字段
	 * @param string $return_type 返回类型
	 * @return array
	 */
	private function getValidates($field, $return_type)
	{
		$validates = $field['validates'] ?? [];

		switch ($return_type) {
			case 'js-validation': // js 使用
			default:
				if (!count($validates)) {
					return [];
				}
				$rule = [];
				if ($validates['required'] ?? false) {
					$rule[] = 'required';
				}
				if ($validates['phone'] ?? false) {
					$rule[] = 'phone';
				}
				if ($validates['email'] ?? false) {
					$rule[] = 'email';
				}
				if ($validates['url'] ?? false) {
					$rule[] = 'url';
				}
				if ($validates['number'] ?? false) {
					$rule[] = 'number';
				}
				if ($validates['date'] ?? false) {
					$rule[] = 'date';
				}
				if ($validates['identity'] ?? false) {
					$rule[] = 'identity';
				}

				return [
					'lay-verify' => implode('|', $rule),
				];
				break;
			case 'laravel': // framework 使用
				if (!count($validates)) {
					return [];
				}
				$rule = [];
				if ($validates['required'] ?? false) {
					$rule[] = Rule::required();
				}
				if ($validates['phone'] ?? false) {
					$rule[] = Rule::mobile();
				}
				if ($validates['email'] ?? false) {
					$rule[] = Rule::email();
				}
				if ($validates['url'] ?? false) {
					$rule[] = Rule::url();
				}
				if ($validates['number'] ?? false) {
					$rule[] = Rule::numeric();
				}
				if ($validates['date'] ?? false) {
					$rule[] = Rule::date();
				}
				if ($validates['chid'] ?? false) {
					$rule[] = Rule::chid();
				}
				if (isset($validates['min'])) {
					$rule[] = Rule::numeric();
					$rule[] = Rule::min((int) $validates['min']);
				}
				if (isset($validates['max'])) {
					$rule[] = Rule::numeric();
					$rule[] = Rule::max((int) $validates['max']);
				}

				return array_unique($rule);
				break;
		}
	}
}