<?php namespace Poppy\System\Classes\Inspect;

use Curl\Curl;
use ErrorException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\ArrayHelper;
use Storage;
use Poppy\System\Classes\Contracts\Api\ApiFactory;
use Throwable;

class ApiParser
{
	use AppTrait;

	/**
	 * @var string 请求类型
	 */
	private $type;

	/**
	 * @var array 包含URL定义的信息
	 */
	private $definition;

	/**
	 * @var string 标题
	 */
	private $title;

	/**
	 * @var string 描述
	 */
	private $description;

	/**
	 * @var string 状态描述
	 */
	private $statusDesc;

	/**
	 * @var string 相应信息
	 */
	private $resp;

	/**
	 * @var int 请求的状态码
	 */
	private $statusCode;

	/**
	 * @var string 请求地址
	 */
	private $url;

	/**
	 * @var array 当前请求日志
	 */
	private $currentLog = [];

	/**
	 * @var array 所有日志
	 */
	private $logs = [];

	/**
	 * @var string 请求的URL地址
	 */
	private $baseUrl;

	/**
	 * @var array 参数
	 */
	private $params = [];

	/**
	 * @var string 请求方法
	 */
	private $method;

	/**
	 * @var ApiFactory API 工厂
	 */
	private $factory;

	/**
	 * ApiParser constructor.
	 * @param string $type 请求类型
	 * @param string $url  请求地址
	 * @throws ApplicationException
	 * @throws FileNotFoundException
	 * @throws ErrorException
	 */
	public function __construct($type, $url = '')
	{
		$this->type    = $type;
		$this->baseUrl = $url ?: config('app.url');

		if (!$this->type) {
			throw new ApplicationException('Empty Type Input');
		}
		$types = array_keys(config('module.system.apidoc'));
		if (!in_array($this->type, $types, true)) {
			throw new ApplicationException('Error Type In Apidoc');
		}

		$jsonPath = '/docs/' . $type . '/api_data.json';
		if ($url) {
			$apiJson = $url . $jsonPath;
			$Curl    = new Curl();
			if ($definition = $Curl->get($apiJson)) {
				if (!$definition) {
					throw new ApplicationException($Curl->errorMessage);
				}
				$this->definition = is_array($definition) ? $definition : json_decode($definition);
			}
		}
		else {
			$Disk = Storage::disk('public');
			if (!$Disk->exists($jsonPath)) {
				throw new ApplicationException('File Not Exists At `' . $jsonPath . '`!');
			}
			if ($definition = $Disk->get($jsonPath)) {
				$this->definition = json_decode($definition);
			}
		}

		$factory = config('module.system.apidoc.' . $type . '.factory');

		$requestUrl = $url ?: config('app.url');

		if ($factory) {
			if (class_exists($factory)) {
				$this->factory = new $factory($requestUrl);
				if (!($this->factory instanceof ApiFactory)) {
					throw new ApplicationException('Token Not implements ApiToken');
				}
			}
			else {
				throw new ApplicationException('Factory `' . $factory . '` Not Exits');
			}
		}
	}

	/**
	 * @return array
	 */
	public function getCurrentLog(): array
	{
		return $this->currentLog;
	}

	/**
	 * @return array
	 */
	public function getLogs(): array
	{
		return $this->logs;
	}

	/**
	 * @return array
	 */
	public function getDefinition(): array
	{
		return $this->definition;
	}

	/**
	 * 获取 Token
	 * @return string Token
	 */
	public function token(): string
	{
		return $this->factory->getToken();
	}

	public function sendRequest($definition): bool
	{
		static $num;
		$method            = data_get($definition, 'type', 'get');
		$this->url         = $this->baseUrl . '/' . data_get($definition, 'url');
		$this->title       = data_get($definition, 'title');
		$this->description = strip_tags(data_get($definition, 'description'));
		$this->factory->setDefinition($definition);
		/* todo 这里需要生成请求参数
		 * ---------------------------------------- */
		if (!$num) {
			$num = 1;
		}
		else {
			$num++;
		}

		$this->params = $this->factory->genParams();
		if ($this->factory->jump($this->url)) {
			return true;
		}
		try {
			$Curl = new Curl();
			$Curl->setTimeout(3);
			$Curl->setHeaders($this->factory->getHeaders());
			if ($method === 'get') {
				$this->method     = 'get';
				$this->resp       = $Curl->get($this->url, $this->params);
				$this->statusCode = $Curl->getHttpStatusCode();
				$this->statusDesc = $Curl->getHttpErrorMessage();
			}
			else {
				$this->method     = 'post';
				$this->resp       = $Curl->post($this->url, $this->params);
				$this->statusCode = $Curl->getHttpStatusCode();
				$this->statusDesc = $Curl->getHttpErrorMessage();
			}

			$this->log();

			if ($this->statusCode !== 200) {
				return $this->setError($this->type . " {$num} Url:[ {$this->title} ]`" . data_get($definition, 'url') . '` Result:' . $this->statusCode);
			}
			$this->success = $this->type . " {$num} Url:[ {$this->title} ]`" . data_get($definition, 'url') . '` Result:' . $this->statusCode;

			return true;
		} catch (Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 记录日志
	 */
	private function log(): void
	{
		$resp = [];
		if ($this->statusCode === 200) {
			$resp = json_decode(json_encode($this->resp, JSON_UNESCAPED_UNICODE), true);
		}

		$this->currentLog = [
			'title'            => $this->title,
			'url'              => $this->url,
			'method'           => $this->method,
			'params'           => ArrayHelper::toKvStr($this->params, '&'),
			'link'             => $this->url . '?' . ArrayHelper::toKvStr($this->params, '&'),
			'description'      => $this->description,
			'resp_status'      => $resp['status'] ?? '',
			'resp_description' => $resp['message'] ?? '',
			'status_code'      => $this->statusCode,
			'status_message'   => $this->statusDesc,
		];
		$this->logs[]     = $this->currentLog;
	}
}