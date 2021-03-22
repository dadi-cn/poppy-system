<?php

namespace Poppy\System\Tests\Setting;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\System\Models\SysConfig;

class SettingTest extends TestCase
{

    /**
     * @throws FakerException
     */
    public function testSet()
    {
        $key  = function () {
            $faker = $this->pyFaker();
            return 'testing::' . $faker->regexify('[a-z]{3,5}') . '.' . $faker->regexify('/[a-z]{5,8}/');
        };
        $item = sys_setting($key());
        $this->assertNull($item);
        $item = sys_setting($key(), '');
        $this->assertEmpty($item);
        $item = sys_setting($key(), 'testing');
        $this->assertEquals('testing', $item);
    }

    public function tearDown(): void
    {
        SysConfig::where('namespace', 'testing')->delete();
    }
}