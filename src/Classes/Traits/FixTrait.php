<?php

namespace Poppy\System\Classes\Traits;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * 修复
 */
trait FixTrait
{
	/**
	 * @var array $fix fix
	 */
	protected $fix = [
		'max'     => 0,
		'min'     => 0,
		'section' => 1,
		'start'   => 1,
		'cached'  => 0,
		'total'   => 0,
		'lastId'  => 0,
		'left'    => 0,
		'method'  => 0,
	];

	/**
	 * 初始化
	 */
	protected function fixInit()
	{
		// 最大id
		$this->fix['max'] = input('max', 0);
		// 最小id
		$this->fix['min'] = input('min', 0);
		// 每次更新数量
		$this->fix['section'] = (int) (input('section', 1) ?: 1);
		// 需要更新的 start_id
		$this->fix['start'] = (int) (input('start', 0) ?: 1);
		// 是否缓存过
		$this->fix['cached'] = input('cache', 0);
		// 需要更新的总量
		$this->fix['total'] = input('total', 0);
	}

	/**
	 * 返回修复的页面
	 * @return Factory|View
	 */
	protected function fixView()
	{
		if ($this->fix['total']) {
			$percentage = round((($this->fix['total'] - $this->fix['left']) / $this->fix['total']) * 100);
		}
		else {
			$percentage = '0';
		}

		$url = route_url('', null, [
			'max'     => $this->fix['max'],
			'min'     => $this->fix['min'],
			'section' => $this->fix['section'],
			'start'   => $this->fix['lastId'],
			'total'   => $this->fix['total'],
			'cache'   => $this->fix['cached'],
			'method'  => $this->fix['method'],
		]);

		return view('py-mgr-page::tpl.progress', [
			'total'         => $this->fix['total'],
			'section'       => $this->fix['section'],
			'left'          => $this->fix['left'],
			'percentage'    => $percentage,
			'continue_time' => 500, // ms 毫秒
			'continue_url'  => $url,
		]);
	}
}