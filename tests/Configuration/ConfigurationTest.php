<?php

namespace Poppy\System\Tests\Configuration;

use Poppy\Framework\Application\TestCase;

class ConfigurationTest extends TestCase
{

    public function testSettings()
    {
        $pamPrefix = sys_setting('py-system::pam.prefix');
        $this->assertNotEquals('LOCAL', $pamPrefix, 'Pam prefix not set.Default value is `LOCAL`');
    }
}