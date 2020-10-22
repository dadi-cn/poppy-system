<?php namespace Poppy\System\Http\Request\Develop;

use Curl\Curl;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\FileHelper;
use Poppy\Framework\Helper\StrHelper;
use Session;

/**
 * Api 文档控制器
 */
class ApiDocController extends DevelopController
{
	/**
	 * @var array 菜单项, 用于右侧展示
	 */
	protected $selfMenu;

	public function __construct()
	{
		parent::__construct();
		$catlog = config('module.system.apidoc');

		if (count($catlog)) {
			foreach ($catlog as $k_cat => $v_cat) {
				if (isset($v_cat['title']) && $v_cat['title']) {
					$this->selfMenu[$v_cat['title']] = config('app.url') . '/docs/' . $k_cat;
				}
			}
		}
		$this->selfMenu['apiDoc'] = 'http://apidocjs.com';
		\View::share([
			'self_menu' => $this->selfMenu,
		]);
	}

	/**
	 * 自动生成接口
	 * @param string $type 支持的类型
	 * @return JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function auto($type = '')
	{
		$catalog = config('module.system.apidoc');
		if (!$catalog) {
			return Resp::error('尚未配置 apidoc 生成目录');
		}

		if (!$type) {
			$keys = array_keys($catalog);
			$type = $keys[0];
		}
		$definition = $catalog[$type];
		$apiDocUrl  = url('docs/' . $type);

		$this->seo('Restful-' . $type, '优雅的在线接口调试方案');

		$tokenGet = function ($key) use ($type) {
			if (Session::has($key)) {
				$token = Session::get($key);
				if (in_array($type, ['web', 'backend'])) {
					// check token is valid
					$curl   = new Curl();
					$access = route('system:pam.auth.access');
					$curl->setHeader('x-requested-with', 'XMLHttpRequest');
					$curl->setHeader('Authorization', 'Bearer ' . $token);
					$item = $curl->post($access);

					if ($curl->httpStatusCode === 401) {
						Session::remove($key);
					}
					if ($curl->httpStatusCode === 200) {
						if ($item->status === 0) {
							$pam = json_decode(json_encode($item->data), true);
							\View::share('pam', $pam);
						}
					}
				}
			}

			return Session::get($key);
		};

		try {
			$index   = input('url');
			$version = input('version', '1.0.0');
			$method  = input('method', 'get');

			$data      = $this->apiData($type, $index, $method, $version);
			$variables = [];
			if (isset($data['current_params'])) {
				foreach ($data['current_params'] as $current_param) {
					if (!isset($data['params'][$current_param->field]) && !$current_param->optional) {
						if (Str::startsWith($current_param->field, ':')) {
							$variableName             = trim($current_param->field, ':');
							$values                   = StrHelper::parseKey(strip_tags($current_param->description));
							$variables[$variableName] = $values;
						}
						else {
							$data['params'][$current_param->field] = $this->getParamValue($current_param);
						}
					}
				}
			}
			$data['version'] = 'v' . substr($version, 0, strpos($version, '.'));

			$key           = 'Success 200';
			$successFields = data_get($data['current'], 'success.fields');

			if ($successFields && $successFields->$key) {
				$success = $data['current']->success->fields->$key;
			}
			else {
				$success = [];
			}
			$data['token'] = $tokenGet('dev_token#' . $type);

			// user
			$user  = [];
			$front = [];
			if (!isset($data['current'])) {
				return Resp::error('没有找到对应 URL 地址');
			}

			return view('poppy-system::develop.api_doc.auto', [
				'guard'      => $type,
				'data'       => $data,
				'variables'  => $variables,
				'success'    => $success,
				'definition' => $definition,
				'apidoc_url' => $apiDocUrl,
				'user'       => $user,
				'front'      => $front,
				'api_url'    => config('app.url'),
			]);
		} catch (Exception $e) {
			return Resp::error($e->getMessage());
		}
	}

	/**
	 * 获取生成的 api 数据
	 * @param string $type    类型
	 * @param null   $prefix  前缀
	 * @param string $method  方法
	 * @param string $version 版本
	 * @return array
	 */
	protected function apiData($type, $prefix = null, $method = 'get', $version = '1.0.0')
	{
		$catalog  = config('module.system.apidoc');
		$docs     = $catalog[$type];
		$jsonFile = base_path('public/docs/' . $type . '/api_data.json');
		$data     = [];
		if (file_exists($jsonFile)) {
			$data['file_exists'] = true;
			$data['url_base']    = config('app.url');
			$data['content']     = FileHelper::getJson($jsonFile, false);
			$content             = new Collection($data['content']);
			$group               = $content->groupBy('groupTitle');
			// add 排序
			$group            = $group->map(function (\Illuminate\Support\Collection $group) {
				return $group->sortBy('title');
			});
			$data['group']    = $group;
			$data['versions'] = [];
			$url              = $prefix;
			if (!$url) {
				$url    = '/' . trim($docs['default_url'] ?? '', '/');
				$method = $docs['method'] ?? 'get';
			}
			if ($url) {
				foreach ($content as $key => $val) {
					$valUrl = trim($val->url, '/');
					$url    = trim($url, '/');
					if ($val->type === $method && $valUrl === $url && $val->version === $version) {
						$data['index']   = $url;
						$data['current'] = $val;

						if (isset($data['current']->parameter)) {
							$data['current_params'] = $data['current']->parameter->fields->Parameter;
							// $data['params']         = $this->params($data['current']);
						}
					}
					if ($val->type === $method && $valUrl === $url) {
						$vk                          = substr($val->version, 0, strpos($val->version, '.'));
						$data['versions']['v' . $vk] = $val->version;
					}
				}
			}
		}
		else {
			$data['file_exists'] = false;
		}
		if (isset($data['versions'])) {
			ksort($data['versions']);
		}

		return $data;
	}

	/**
	 * 获取随机参数值
	 * @param string $param 参数
	 * @return int|string
	 */
	protected function getParamValue($param)
	{
		/*
		"group": "Parameter"
		"type": "<p>String</p> "
		"optional": false
		"field": "device_id"
		"size": "2..5"
		"description": "<p>设备ID, 设备唯一的序列号</p> "
		 */
		$type          = strtolower(strip_tags(trim($param->type)));
		$allowedValues = $param->allowedValues ?? [];
		$size          = $param->size ?? '';
		switch ($type) {
			case 'string':
				if (strpos($size, '..') !== false) {
					list($start, $end) = explode('..', $size);
					$start = (int) $start;
					$end   = (int) $end;

					$length = rand($start, $end);

					return StrHelper::random($length);
				}
				if ($allowedValues) {
					shuffle($allowedValues);

					return $allowedValues[0];
				}

				return '';
				break;
			case 'boolean':
				return rand(0, 1);
				break;
			case 'number':
				if (strpos($size, '-') !== false) {
					list($start, $end) = explode('-', $size);
					$start = (int) $start;
					$end   = (int) $end;

					return rand($start, $end);
				}
				if (strpos($size, '..') !== false) {
					list($start, $end) = explode('..', $size);
					$start = (int) $start;
					$end   = (int) $end;

					$start = ((int) str_pad(1, $start, 0));
					$end   = ((int) str_pad(1, $end + 1, 0)) - 1;

					return rand($start, $end);
				}
				if ($allowedValues) {
					shuffle($allowedValues);

					return $allowedValues[0];
				}

				return rand(0, 99999999);
				break;
		}

		return '';
	}

	/**
	 * 设置
	 * @param string $type  类型
	 * @param string $field 字段
	 * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
	 */
	public function field($type, $field)
	{
		$sessionKey = 'dev_token#' . $type . '#' . $field;
		if (is_post()) {
			$token = input('token');
			if (!$token) {
				return Resp::error($field . '不能为空');
			}
			Session::remove($sessionKey);
			Session::put($sessionKey, $token);

			return Resp::success('设置 ' . $field . ' 成功', 'top_reload|1');
		}
		$value = Session::get($sessionKey);

		return view('poppy-system::develop.api_doc.field', compact('type', 'value', 'field'));
	}
}