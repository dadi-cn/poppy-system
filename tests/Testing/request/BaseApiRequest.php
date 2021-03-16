<?php

namespace Poppy\System\Tests\Testing\Request;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Str;
use Throwable;

/**
 * 生成Token
 */
class BaseApiRequest
{
    protected $jumpUrl = [];

    /**
     * @var string 请求的Token
     */
    protected static $token;

    /**
     * @var array 请求头
     */
    protected static $headers = [];

    /**
     * @var object 定义的类型
     */
    protected $definition;

    /**
     * WebApiRequest constructor.
     * @param string $url 请求的地址
     * @throws Throwable
     */
    public function __construct($url)
    {
        $this->login($url);
    }

    /**
     * @return string Token
     */
    public function getToken(): string
    {
        return self::$token;
    }

    /**
     * 获取 Header
     * @return array
     */
    public function getHeaders(): array
    {
        return self::$headers;
    }

    /**
     * 设置定义项目
     * @param object $definition
     */
    public function setDefinition($definition): void
    {
        $this->definition = $definition;
    }

    /**
     * 跳过的URL
     * @param string $url 请求的地址
     * @return bool
     */
    public function jump($url): bool
    {
        if (Str::contains($url, $this->jumpUrl)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $type        类型
     * @param string $description 描述
     * @param bool   $optional    是否可选
     * @return float|int|string
     */
    protected function genParamFromType($type, $description, $optional = false)
    {
        switch ($type) {
            case 'Int':
            case 'Integer':
                return $this->faker()->randomNumber(0, 999);
                break;
            case 'Float':
                return $this->faker()->randomFloat(2, 0, 999);
                break;
            case 'String':
                return $this->faker()->word;
                break;
        }
    }

    /**
     * 返回 Faker
     * @return Generator
     */
    protected function faker(): Generator
    {
        static $faker;

        return $faker ?: $faker = Factory::create('zh_cn');
    }
}