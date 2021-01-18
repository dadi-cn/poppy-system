<?php namespace Poppy\System\Tests\Base;

use Poppy\Framework\Application\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * 检测系统文件是否存在
     */
    public function testSystemManagementFile()
    {
        $file = 'resources/assets/images/logo.png';
        $this->assertFileExists(base_path($file), "Logo file `{$file}` not exists! Suggest size is 145x40");

        $systemFile = 'modules/system/backend/images/loading.svg';
        $this->assertFileExists(
            public_path($systemFile),
            "System management file `{$systemFile}` not exists! 
			Run `yarn build` in base path `modules/system/resources/mixes/backend`"
        );
    }

    public function testSettings()
    {
        $pamPrefix = sys_setting('py-system::pam.prefix');
        $this->assertNotEquals('LOCAL', $pamPrefix, 'Pam prefix not set.Default value is `LOCAL`');
    }
}