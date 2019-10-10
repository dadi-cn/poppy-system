<?php namespace Poppy\System\Jobs;

use Curl\Curl;
use ErrorException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Poppy\Framework\Application\Job;
use Poppy\Framework\Helper\ArrayHelper;
use stdClass;
use Poppy\System\Classes\Traits\ListenerTrait;

/**
 * 回调执行
 */
class NotifyJob extends Job implements ShouldQueue
{
	use ListenerTrait, Queueable;

	/**
	 * @var string 请求网址
	 */
	private $url;

	/**
	 * @var string 请求方法
	 */
	private $method;

	/**
	 * @var array 请求参数
	 */
	private $params;

	/**
	 * @var int 请求次数
	 */
	private $execNum;

	/**
	 * 统计用户计算数量
	 * @param string $url      请求的URL 地址
	 * @param string $method   请求的方法
	 * @param array  $params   请求的参数
	 * @param int    $exec_num 请求次数
	 */
	public function __construct($url, $method, $params = [], $exec_num = 0)
	{
		$this->url     = $url;
		$this->method  = $method;
		$this->params  = $params;
		$this->execNum = $exec_num;
	}

	/**
	 * 执行
	 * @throws ErrorException
	 */
	public function handle()
	{
		app('setting')->setReRead(true);

		/* 重发次数
		 -------------------------------------------- */
		if ($time = sys_setting('system::callback.exam_time')) {
			$timeMap = explode(',', $time);
		}
		else {
			$timeMap = [
				0 => 10,
				1 => 30,
				2 => 60,
			];
		}

		$log = function ($result) {
			if ($result instanceof stdClass) {
				$result = json_decode(json_encode($result), true);
				$result = ArrayHelper::toKvStr($result);
			}
			$kvParams = ArrayHelper::toKvStr($this->params);

			return ($this->execNum + 1) . '\'s Request:' . "url : {$this->url}, method : {$this->method}, params : {$kvParams}, result : " . $result;
		};

		$curl = new Curl();
		$curl->setTimeout(10);
		if ($this->method === 'post') {
			$resp = $curl->post($this->url, $this->params);
		}
		else {
			$resp = $curl->get($this->url, $this->params);
		}

		if ($curl->errorCode) {
			if ($this->execNum < count($timeMap)) {
				Log::info(sys_mark('system', self::class, $log($curl->errorMessage)));
				dispatch((new self($this->url, $this->method, $this->params, $this->execNum + 1))->delay($timeMap[$this->execNum]));
			}
			else {
				Log::error(sys_mark('system', self::class, $log($curl->errorMessage)));
			}
		}
		else {
			Log::info(sys_mark('system', self::class, $log($resp)));
		}
	}
}