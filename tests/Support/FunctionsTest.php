<?php namespace Poppy\System\Tests\Support;

use Poppy\Framework\Application\TestCase;

class FunctionsTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        sys_cache('py-core')->forget('module.repo.hooks');
        sys_cache('py-core')->forget('module.repo.module');
    }

    public function testSysGet()
    {
        $input = [
            'null'         => null,
            'int'          => 1,
            'string'       => 'string',
            'string_space' => 'string    ',
        ];
        $arr   = sys_get($input, ['null', 'int', 'string', 'string_space']);
        $this->assertEquals([
            'null'         => '',
            'int'          => 1,
            'string'       => 'string',
            'string_space' => 'string',
        ], $arr);
    }
}