<?php namespace Poppy\System\Classes\Contracts\Api;

interface ApiFactory
{
	/**
	 * ApiFactory constructor.
	 * @param $url string 请求地址
	 */
	public function __construct($url);

	/**
	 * 获取Token
	 * @return string
	 */
	public function getToken(): string;

	/**
	 * 获取 Headers
	 * @return array
	 */
	public function getHeaders(): array;

	/**
	 * @param object $definition 定义项目
	 */
	public function setDefinition($definition): void;

	/**
	 * 生成请求数组
	 * @param array $params 请求参数
	 * @return array
	 */
	public function genParams($params = []): array;

	/**
	 * 跳过地址不去执行
	 * @param string $url OSS
	 * @return bool
	 */
	public function jump($url): bool;
}