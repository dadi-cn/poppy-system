<?php

namespace Poppy\System\Tests\Setting;

use Exception;
use Poppy\Framework\Application\TestCase;

class SettingTest extends TestCase
{

    public function testGet()
    {
        $item = sys_setting($this->randKey('set'));
        $this->assertNull($item);
        $item = sys_setting($this->randKey('set'), '');
        $this->assertEmpty($item);
        $item = sys_setting($this->randKey('set'), 'testing');
        $this->assertEquals('testing', $item);
    }

    public function testGetGn()
    {
        app('poppy.system.setting')->removeNG('testing::set');
        app('poppy.system.setting')->set($this->randKey('set'), $this->faker()->lexify());
        app('poppy.system.setting')->set($this->randKey('set'), $this->faker()->lexify());
        $gn = app('poppy.system.setting')->getNG('testing::set');
        $this->assertCount(2, $gn);
    }

    /**
     * @throws Exception
     */
    public function tearDown(): void
    {
        app('poppy.system.setting')->removeNG('testing::set');
    }

    private function randKey($group = ''): string
    {
        $faker = $this->faker();
        return 'testing::' . ($group ? $group : $faker->regexify('[a-z]{3,5}')) . '.' . $faker->regexify('/[a-z]{5,8}/');
    }
}