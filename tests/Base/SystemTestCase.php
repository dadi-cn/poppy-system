<?php namespace Poppy\System\Tests\Base;

use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Str;
use Log;
use Poppy\Faker\Generator;
use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\Contracts\ApiSignContract;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\SysCaptcha;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class SystemTestCase extends TestCase
{
    protected $headers = [];

    protected $loginParams = [
        'passport' => '',
        'password' => '',
    ];

    /**
     * @var array 请求返回的数据
     */
    protected $data;

    /**
     * @var array 请求参数
     */
    protected $params;

    /**
     * 当前请求url
     * @var string
     */
    protected $request = '';

    /**
     * @var PamAccount
     */
    protected $pam;

    /**
     * 控制台输出
     * @var array
     */
    protected $reportType = ['log', 'console'];

    public function setUp(): void
    {
        parent::setUp();
        DB::enableQueryLog();
        DB::beginTransaction();
    }

    public function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * 测试日志
     * @param bool   $result  测试结果
     * @param string $message 测试消息
     * @param mixed  $context 上下文信息, 数组
     * @return string
     */
    public function runLog($result = true, $message = '', $context = null): string
    {
        $type    = $result ? '[Success]' : '[ Error ]';
        $message = 'Test : ' . $type . $message;
        if ($context instanceof Arrayable) {
            $context = $context->toArray();
        }
        if (in_array('log', $this->reportType(), true)) {
            Log::info($message, $context ?: []);
        }
        if (in_array('console', $this->reportType(), true)) {
            dump([
                'message' => $message,
                'context' => $context ?: [],
            ]);
        }

        return $message;
    }

    /**
     * 测试基准URL
     * @param string $url
     * @param string $version
     * @return string
     */
    protected function apiUrl($url, $version = 'v1'): string
    {
        return $this->request = "/api_{$version}/" . $url;
    }

    /**
     * Setting request header
     * @param null  $token
     * @param array $addition
     * @return array
     */
    protected function headers($token = null, array $addition = []): array
    {
        $headers = [];
        //拼接 token
        if (null !== $token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        if ($addition) {
            $headers = array_merge($headers, $addition);
        }

        $this->headers = $headers;

        return $this->headers;
    }

    protected function webLogin()
    {
        $response = $this->jsonPost('pam/auth/login', 'v1', $this->loginParams);

        $result = json_decode($response->getContent());

        //获取用户 token
        $this->headers = $this->headers(data_get($result, 'data.token'));
    }

    /**
     * Post 请求地址
     * @param string $url     URL 地址
     * @param string $version 版本
     * @param array  $params  参数
     * @return TestResponse
     */
    protected function jsonPost($url, $version = 'v1', array $params = []): TestResponse
    {
        $this->request             = $this->apiUrl($url, $version);
        $this->params              = $params;
        $this->params['timestamp'] = Carbon::now()->timestamp;
        $token                     = str_replace('Bearer ', '', $this->headers['Authorization'] ?? '');
        $this->params['token']     = $token;
        /** @var ApiSignContract $Sign */
        $Sign                 = app('poppy.system.api_sign');
        $this->params['sign'] = $Sign->sign($this->params);
        $resp                 = $this->json('POST', $this->request, $this->params, $this->headers);
        $resp->assertStatus(200);

        return $resp;
    }

    protected function initPam($username = '')
    {
        $username = $username ?: $this->env('pam');
        $pam      = PamAccount::passport($username);
        $this->assertNotNull($pam, 'Testing user pam is not exist');
        $this->pam = $pam;
        $token     = auth('jwt')->fromUser($pam);
        $this->headers($token);
    }

    /**
     * @param TestResponse $response
     */
    protected function assertStatusSuccess($response)
    {
        $json = json_decode($response->getContent(), true);
        $this->assertSame($json['status'], 0, 'Resp expected success, but failed, reason:' . $json['message']);
        if ($json['status'] !== 0) {
            $this->respReport($json);
        }
    }

    /**
     * @param TestResponse $response
     * @return TestResponse
     */
    protected function assertStatusFail($response): TestResponse
    {
        $json = json_decode($response->getContent(), true);
        $this->assertNotSame($json['status'], 0, 'This request need fail, but successed!');

        return $response;
    }

    /**
     * 数据值获取
     * @param string $key
     * @return mixed
     */
    protected function dataGet($key)
    {
        return data_get($this->data, $key);
    }

    /**
     * 检测所有值是不是null
     * @param        $array
     * @param string $remember
     */
    protected function checkNull($array, $remember = '')
    {
        if ($array instanceof Arrayable) {
            $array = $array->toArray();
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->checkNull($value, $remember);
            }
            else {
                $remember = $key . '.';
                // 末尾以 at 结尾的均识别为
                if (Str::endsWith($key, '_at')) {
                    continue;
                }
                $this->assertNull(
                    $value,
                    'Json value is null @' . rtrim($remember, '.')
                );
            }
        }
    }

    /**
     * @return Generator|Application|mixed
     * @throws FakerException
     */
    protected function faker()
    {
        return py_faker();
    }

    /**
     * 设置环境变量
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    protected function env($key = '', $default = '')
    {
        if (!$key) {
            return '';
        }

        return env('TESTING_' . strtoupper($key), $default);
    }

    /**
     * 读取模块 Json 文件
     * @param $module
     * @param $path
     * @return array
     */
    protected function readJson($module, $path): array
    {
        $filePath = poppy_path($module, $path);
        if (file_exists($filePath)) {
            $config = file_get_contents($filePath);
            if (UtilHelper::isJson($config)) {
                return json_decode($config, true);
            }
            return [];
        }
        return [];
    }


    protected function sysCaptcha($passport, $type = 'login')
    {
        $resp = $this->jsonPost('util/captcha/send', [
            'passport'   => $passport,
            'type'       => $type,
            'image_code' => Str::random(4),
        ]);
        $this->assertStatusSuccess($resp);

        return SysCaptcha::where('passport', $passport)->value('captcha');
    }

    /**
     * @param TestResponse $resp
     */
    protected function respReport($resp)
    {
        $paramsStr = json_encode($this->params, JSON_UNESCAPED_UNICODE);
        $data      = [
            'url'    => $this->request,
            'params' => $paramsStr,
            'resp'   => $resp->content(),
        ];

        if (in_array('log', $this->reportType(), true)) {
            Log::debug($data);
        }
        if (in_array('console', $this->reportType(), true)) {
            /** @noinspection ForgottenDebugOutputInspection */
            var_dump($data);
        }
    }

    /**
     * 汇报类型
     * @return array
     */
    protected function reportType(): array
    {
        $reportType = $this->env('report_type');
        if ($reportType) {
            return StrHelper::separate(',', $reportType);
        }

        return $this->reportType;
    }

    /**
     * SQL Log 提示
     */
    protected function sqlLog(): void
    {
        $logs = DB::getQueryLog();

        if (count($logs)) {
            $formats = [];
            foreach ($logs as $log) {
                $query = $log['query'];
                if (count($log['bindings'] ?? [])) {
                    foreach ($log['bindings'] as $binding) {
                        if (is_string($binding)) {
                            $binding = '"' . $binding . '"';
                        }
                        $query = Str::replaceFirst('?', $binding, $query);
                    }
                }
                $time      = $log['time'] ?? 0;
                $formats[] = [
                    $query, $time,
                ];
            }
            $this->table(['Query', 'Time'], $formats);
        }
    }

    /**
     * Format input to textual table.
     *
     * @param array           $headers
     * @param Arrayable|array $rows
     * @param string          $tableStyle
     * @param array           $columnStyles
     * @return void
     */
    protected function table($headers, $rows, $tableStyle = 'default', array $columnStyles = []): void
    {
        $table = new Table(new ConsoleOutput());

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        $table->setHeaders((array) $headers)->setRows($rows)->setStyle($tableStyle);

        foreach ($columnStyles as $columnIndex => $columnStyle) {
            $table->setColumnStyle($columnIndex, $columnStyle);
        }

        $table->render();
    }

    /**
     * 输出变量
     * @param array $var
     */
    protected function export($var): void
    {
        $export = var_export($var, true);
        echo $export;
    }

    /**
     * 当前的描述
     * @param string $append 追加的信息
     * @return string
     */
    protected static function desc($append = ''): string
    {
        $bt       = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $function = $bt[1]['function'];
        $class    = $bt[1]['class'];

        return '|' . $class . '@' . $function . '|' . ($append ? $append . '|' : '');
    }
}