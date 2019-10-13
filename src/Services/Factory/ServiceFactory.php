<?php namespace Poppy\System\Services\Factory;

use Form;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Poppy\System\Services\Contracts\ServiceArray;
use Poppy\System\Services\Contracts\ServiceForm;
use Poppy\System\Services\Contracts\ServiceHtml;

/**
 * 服务工厂
 */
class ServiceFactory
{
	/**
	 * 钩子
	 * @param string $id     钩子标示符
	 * @param array  $params 参数
	 * @return null
	 */
	public function parse($id, $params = [])
	{
		$service = app('module')->services()->get($id);
		if (!$service) {
			return null;
		}
		$hooks  = app('module')->hooks()->get($id);
		$method = 'parse' . Str::studly($service['type']);

		if (is_callable([$this, $method])) {
			return $this->$method($hooks, $params);
		}

		return null;
	}

	/**
	 * 分析数组
	 * @param string $hooks  Hook
	 * @param array  $params 参数
	 * @return array
	 */
	private function parseArray($hooks, $params)
	{
		$collect = [];
		collect($hooks)->each(function ($hook) use (&$collect) {
			if (class_exists($hook)) {
				$obj = new $hook();
				if ($obj instanceof ServiceArray) {
					$collect = array_merge($collect, [
						$obj->key() => $obj->data(),
					]);
				}
			}
		});

		return $collect;
	}

	/**
	 * 分析表单
	 * @param string $builder 构建器
	 * @param array  $params  参数
	 * @return HtmlString|mixed
	 */
	private function parseForm($builder, $params)
	{
		if (class_exists($builder)) {
			$obj = new $builder();
			if ($obj instanceof ServiceForm) {
				return $obj->builder($params);
			}
		}

		return Form::text($params['name'], $params['value'], $params['options'] + ['class' => 'layui-input']);
	}

	/**
	 * 解析 Html, 多组
	 * @param string $hooks  钩子
	 * @param array  $params 参数
	 * @return string
	 */
	private function parseHtml($hooks, $params)
	{
		$collect = '';
		collect($hooks)->each(function ($hook) use (&$collect) {
			if (class_exists($hook)) {
				$obj = new $hook();
				if ($obj instanceof ServiceHtml) {
					$collect .= $obj->output();
				}
			}
		});

		return $collect;
	}
}