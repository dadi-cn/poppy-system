<?php

namespace Poppy\System\Tests\Configuration;

use Poppy\System\Tests\Base\SystemTestCase;

class EnvironmentTest extends SystemTestCase
{
    /**
     * 配置项检测
     */
    public function testEnv()
    {
        $env = [
            'JWT_SECRET' => 'Jwt Token 授权会出现获取Token 认证失败的情况',
            'APP_ENV'    => '会出现 logger 无法使用的情况',
            'APP_KEY'    => '会出现 RuntimeException 错误 (The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths)',
        ];

        foreach ($env as $_env => $desc) {
            if (!env($_env)) {
                $this->fail("Env {$_env} 未设置, {$desc}");
            }
            else {
                $this->assertTrue(true);
            }
        }
    }

    public function testCommands()
    {
        $env = [
            'node',
            'apidoc',
        ];

        foreach ($env as $_env) {
            if (!command_exist($_env)) {
                $this->fail("Command {$_env} need to install");
            }
            else {
                $this->assertTrue(true);
            }
        }
    }

    /**
     * 检查PHP 扩展
     */
    public function testPhp()
    {
        $env = [
            'gd',
            'json',
            'iconv',
            'mysqlnd',
            'mbstring',
            'bcmath',
        ];

        foreach ($env as $_env) {
            if (!extension_loaded($_env)) {
                $this->fail("Php extension {$_env} need to load");
            }
            else {
                $this->assertTrue(true);
            }
        }
    }
}
